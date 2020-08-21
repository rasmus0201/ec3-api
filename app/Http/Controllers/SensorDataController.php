<?php

namespace App\Http\Controllers;

use App\Sensor;
use App\SensorData;
use App\Http\Requests\StoreSensorDataPost;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Log;

class SensorDataController extends Controller
{
    private $prPage = 100;

    public function index()
    {
        return response()->json(SensorData::simplePaginate($this->prPage));
    }

    public function create(StoreSensorDataPost $request)
    {
        $validated = $request->validated();
        $deviceId = $validated['deviceId'] ?? 1;
        $data = collect($validated['data']);
        $now = Carbon::now();

        $sensors = Sensor::all();
        $sensorIdsByName = [];
        foreach($sensors as $sensor) {
            $sensorIdsByName[$sensor->name] = $sensor->id;
        }

        $data = $data->map(function($input) use ($deviceId, $now, $sensorIdsByName) {
            $ts = $input['timestamp'];
            $time = Carbon::now();
            $time->setTimestamp(substr($ts, 0, 10));
            $time->setMicroseconds(substr($ts, -3) . "000");

            if ($time > $now->addHours(48)) {
                return [];
            }

            if (!isset($sensorIdsByName[$input['type']])) {
                return [];
            }
            
            $input['device_id'] = $deviceId;
            $input['sensor_id'] = $sensorIdsByName[$input['type']];
            $input['sensored_at'] = $time;
            $input['created_at'] = Carbon::now();
            $input['value'] = round($input['value'], 2);

            unset($input['timestamp']);
            unset($input['type']);

            return $input;
        });

        try {
            SensorData::insert($data->filter()->toArray());
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return response()->json([
                'status' => false
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }

    public function graph(Request $request)
    {
        if ($request->get('start') && $request->get('end')) {
            $start = Carbon::createFromTimestamp($request->get('start'));
            $end = Carbon::createFromTimestamp($request->get('end'));
        } else {
            $start = Carbon::now()->subDays(30)->setTime(0, 0, 0);
            $end = Carbon::now()->setTime(23, 59, 59);
        }

        if ($start >= $end) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $deviceId = $request->get('device_id') ?? 1;
        $delta = $request->get('delta') ?? 60 * 24;
        $data = [];

        $sensors = Sensor::all();
        $sensorsArr = [];

        foreach($sensors as $sensor) {
            $sensorsArr[$sensor->id] = [
                'device_id' => $deviceId,
                'sensor_id' => $sensor->id,
                'sensor' => $sensor->name,
                'count' => 0,
                'avg' => 0
            ];
        }

        while ($start < $end) {
            $ts = $start->timestamp;
            $dataPoints = SensorData::select([
                    'device_id',
                    'sensor_id',
                    DB::raw('count(id) as count'),
                    DB::raw('round(avg(value), 2) as avg')
                ])
                ->where('device_id', $deviceId)
                ->where('sensored_at', '>=', $start)
                ->where('sensored_at', '<', $start->clone()->addMinutes($delta))
                ->groupBy(['device_id', 'sensor_id'])
                ->get()
                ->mapWithKeys(function ($item) use ($sensorsArr) {
                    $item->sensor = $sensorsArr[$item->sensor_id]['sensor'];
                    return [$item->sensor_id => $item];
                })
                ->toArray();

            $missingSensors = array_diff_key($sensorsArr, $dataPoints);
            $data[$ts] = array_merge($dataPoints, $missingSensors);
            $start->addMinutes($delta);
        }
       
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
