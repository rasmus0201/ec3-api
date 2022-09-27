<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Requests\StoreDeviceMeasurementRequest;
use App\Models\{Device, Sensor, SensorMeasurement};
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
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
            $start = Carbon::createFromTimestamp($request->get('start'), 'UTC');
            $end = Carbon::createFromTimestamp($request->get('end'), 'UTC');
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

        $sensors = Sensor::all();
        $sensorsArr = [];
        foreach ($sensors as $sensor) {
            $sensorsArr[$sensor->id] = [
                'id' => $sensor->id,
                'name' => $sensor->name,
            ];
        }

        $sensorNamesById = array_combine(
            array_column($sensorsArr, 'id'),
            array_column($sensorsArr, 'name')
        );

        $interval = new DateInterval("PT{$deltaSeconds}S");
        $intervals = new DatePeriod($start, $interval, $end);

        $timestamps = [];
        foreach ($intervals as $intervalDate) {
            $timestamps[$intervalDate->getTimestamp()] = array_fill_keys(
                array_keys($sensorsArr),
                []
            );
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

        foreach ($dataPoints as $dataPoint) {
            $ts = $dataPoint->measured_at->setSeconds(0)->timestamp;
            $tsOffset = $ts % (60 * 60 * 24);
            $intervalTs = ($ts - $tsOffset) + ($tsOffset - ($tsOffset % $deltaSeconds));

            if (!isset($timestamps[$intervalTs]) || !isset($timestamps[$intervalTs][$dataPoint->sensor_id])) {
                Log::debug("Could not figure out a correct timestamp! data: " . json_encode([
                    'datapoint_ts' => $ts,
                    'start_ts' => $start->timestamp,
                    'delta_seconds' => $deltaSeconds,
                    'calculated_ts' => $intervalTs,
                    'sensor_id' => $dataPoint->sensor_id,
                ]));

                continue;
            }

            $timestamps[$intervalTs][$dataPoint->sensor_id][] = $dataPoint->value;
        }

        $returnData = [];
        foreach ($timestamps as $intervalTs => $sensorCollections) {
            $dataPoints = [];
            foreach ($sensorCollections as $sensorId => $sensorCollection) {
                $dataPoints[$sensorId] = [
                    'sensor_id' => $sensorId,
                    'sensor' => $sensorNamesById[$sensorId],
                    'avg' => count($sensorCollection) > 0 ? round(array_sum($sensorCollection) / count($sensorCollection), 2) : 0.0,
                ];
            }

            $returnData[$intervalTs] = $dataPoints;
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

        $device->load('sensors');

        // Create a map of sensorName => sensorId.
        $sensors = Sensor::all()->mapWithKeys(function (Sensor $value) {
            return [$value->name => $value->id];
        });

        $measurements = $data->map(function ($input) use ($device, $sensors, $now) {
            $ts = $input['ts'];
            $time = Carbon::now();
            $time->setTimestamp(substr($ts, 0, 10));
            $time->setMicroseconds(substr($ts, -3) . '000');

            if ($time > $now->addHours(48)) {
                return [];
            }

            if (!$device->sensors->pluck('name')->contains($input['t'])) {
                return [];
            }

            return [
                'device_id' => $device->id,
                'sensor_id' => $sensors[$input['t']],
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
