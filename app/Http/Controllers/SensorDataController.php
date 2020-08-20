<?php

namespace App\Http\Controllers;

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
        $validated = collect($request->validated()['data']);

        $now = Carbon::now();

        $validated = $validated->map(function($input) use ($now) {
            $ts = $input['timestamp'];
            $time = Carbon::now();
            $time->setTimestamp(substr($ts, 0, 10));
            $time->setMicroseconds(substr($ts, -3) . "000");

            if ($time > $now->addHours(48)) {
                return [];
            }
            
            $input['sensored_at'] = $time;
            $input['created_at'] = Carbon::now();
            $input['updated_at'] = Carbon::now();

            $input['value'] = round($input['value'], 2);
            $input['sensor'] = $input['type'];

            unset($input['timestamp']);
            unset($input['type']);

            return $input;
        });

        $data = $validated->filter()->toArray();

        try {
            SensorData::insert($data);
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
        $defaultData = collect([
            [
                'sensor' => 'temperature',
                'count' => 0,
                'avg' => 0
            ],
            [
                'sensor' => 'humidity',
                'count' => 0,
                'avg' => 0
            ],
            [
                'sensor' => 'light',
                'count' => 0,
                'avg' => 0
            ],
            [
                'sensor' => 'sound',
                'count' => 0,
                'avg' => 0
            ],
        ]);

        if ($request->get('start') && $request->get('end')) {
            $start = Carbon::createFromTimestamp($request->get('start'));
            $end = Carbon::createFromTimestamp($request->get('end'));
        } else {
            $start = Carbon::now()->subDays(7)->setTime(0, 0, 0);
            $end = Carbon::now()->setTime(23, 59, 59);
        }

        if ($start >= $end) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $delta = $request->get('delta') ?? 24;
        $data = [];
        while ($start < $end) {
            $ts = $start->timestamp;
            $dataPoint = SensorData::select([
                    'sensor',
                    DB::raw('count(id) as count'),
                    DB::raw('round(avg(value), 2) as avg')
                ])
                ->where('sensored_at', '>=', $start)
                ->where('sensored_at', '<', $start->clone()->addMinutes($delta))
                ->groupBy('sensor')
                ->get();
           
            $data[$ts] = $dataPoint->union($defaultData);
            $start->addMinutes($delta);
        }
       
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
