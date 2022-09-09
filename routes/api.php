<?php

use App\Http\Controllers\{DeviceController, DeviceMeasurementController, LocationController, SensorController};
use App\Http\JsonResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/test', function () {
        $sensors = ['humidity', 'temperature', 'light', 'sound', 'vibration'];

        $dataPoints = 100;
        $data = [];
        for ($i = 0; $i < $dataPoints; $i++) {
            $data[] = [
                'sensor' => Arr::random($sensors),
                'value' => rand(0, 1023),
                'measured_at' => round(microtime(true) * 1000)
            ];
        }

        return JsonResponseFactory::success([
            'data' => $data,
        ]);
    });

    Route::apiResource('sensors', SensorController::class)->only([
        'index',
        'store',
        'show',
        'update',
    ]);

    Route::apiResource('locations', LocationController::class)->only([
        'index',
        'store',
        'show',
        'update',
    ]);

    Route::apiResource('devices', DeviceController::class)->only([
        'index',
        'store',
        'show',
        'update',
    ]);

    Route::apiResource('devices.measurements', DeviceMeasurementController::class)->only([
        'index',
        'store',
    ]);

    // Route::apiResource('/sensor-data', SensorDataController::class)->only([
    //     'index',
    //     'store',
    // ]);

    // Route::get('/sensor-data/graph', [SensorDataController::class, 'graph']);
});
