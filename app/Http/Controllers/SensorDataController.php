<?php

namespace App\Http\Controllers;

use App\SensorData;
use App\Http\Requests\StoreSensorDataPost;
use Carbon\Carbon;
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
}
