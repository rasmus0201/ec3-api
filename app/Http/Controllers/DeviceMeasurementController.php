<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Requests\StoreDeviceMeasurementRequest;
use App\Models\{Device, Sensor, SensorMeasurement};
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Log};

class DeviceMeasurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Device $device)
    {
        if ($request->get('start') && $request->get('end')) {
            $start = Carbon::createFromTimestamp($request->get('start'));
            $end = Carbon::createFromTimestamp($request->get('end'));
        } else {
            $start = Carbon::now()->subDays(30)->setTime(0, 0, 0);
            $end = Carbon::now()->setTime(23, 59, 59);
        }

        $start->setSeconds(0);
        $deltaSeconds = 60 * ((int) ($request->get('delta') ?? 60 * 24));

        if ($start >= $end || $deltaSeconds < 1) {
            return JsonResponseFactory::error([
                'data' => []
            ]);
        }

        $data = [];
        $sensors = Sensor::all();
        $sensorsArr = [];
        foreach ($sensors as $sensor) {
            $sensorsArr[$sensor->id] = [
                'device_id' => $device->id,
                'sensor_id' => $sensor->id,
                'sensor' => $sensor->name,
                'avg' => 0
            ];
        }

        $sensorNamesById = array_combine(
            array_column($sensorsArr, 'sensor_id'),
            array_column($sensorsArr, 'sensor')
        );

        $timestamps = [];
        $currentTs = $start->clone();
        while ($currentTs < $end) {
            $timestamps[$currentTs->timestamp] = $currentTs->clone()->addSeconds($deltaSeconds)->timestamp;
            $currentTs->addSeconds($deltaSeconds);
        }

        $dataPoints = SensorMeasurement::select([
            'sensor_id',
            'measured_at',
            DB::raw('avg(value) as value')
        ])
            ->where('device_id', $device->id)
            ->where('measured_at', '>=', $start)
            ->where('measured_at', '<', $end)
            ->groupBy(['sensor_id', 'measured_at'])
            ->orderBy('measured_at', 'DESC')
            ->cursor();

        $data = [];
        foreach ($dataPoints as $dataPoint) {
            $ts = $dataPoint->measured_at->setSeconds(0)->timestamp;
            $tsOffset = $ts % (60 * 60 * 24);
            $intervalTs = ($ts - $tsOffset) + ($tsOffset - ($tsOffset % $deltaSeconds));

            if (!isset($timestamps[$intervalTs])) {
                Log::debug("Could not figure out a correct timestamp! data: " . json_encode([
                    'datapoint_ts' => $ts,
                    'start_ts' => $start->timestamp,
                    'delta_seconds' => $deltaSeconds,
                    'calculated_ts' => $intervalTs
                ]));

                continue;
            }

            if (!isset($data[$intervalTs])) {
                $data[$intervalTs] = [];
            }

            if (!isset($data[$intervalTs][$dataPoint->sensor_id])) {
                $data[$intervalTs][$dataPoint->sensor_id] = [];
            }

            $data[$intervalTs][$dataPoint->sensor_id][] = $dataPoint->value;
        }

        $returnData = [];
        foreach ($data as $intervalTs => $sensorCollections) {
            $dataPoints = [];
            foreach ($sensorCollections as $sensorId => $sensorCollection) {
                $dataPoints[$sensorId] = [
                    'device_id' => $device->id,
                    'sensor_id' => $sensorId,
                    'sensor' => $sensorNamesById[$sensorId],
                    'avg' => round(array_sum($sensorCollection) / count($sensorCollection), 2)
                ];
            }

            $missingSensors = array_diff_key($sensorsArr, $dataPoints);
            $returnData[$intervalTs] = array_merge($dataPoints, $missingSensors);
        }

        return JsonResponseFactory::success([
            'data' => $returnData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceMeasurementRequest $request, Device $device): JsonResponse
    {
        $now = Carbon::now();
        $data = collect($request->validated('data'));

        $measurements = $data->map(function ($input) use ($device, $now) {
            $ts = $input['ts'];
            $time = Carbon::now();
            $time->setTimestamp(substr($ts, 0, 10));
            $time->setMicroseconds(substr($ts, -3) . '000');

            if ($time > $now->addHours(48)) {
                return [];
            }

            return [
                'device_id' => $device->id,
                'sensor_id' => $input['sid'],
                'value' => round($input['v'], 2),
                'measured_at' => $time,
                'created_at' => Carbon::now(),
            ];
        });

        try {
            SensorMeasurement::insert($measurements->filter()->toArray());
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return JsonResponseFactory::error();
        }

        return JsonResponseFactory::created();
    }
}
