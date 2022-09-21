<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Requests\StoreImageRequest;
use App\Models\{Device, Image, Sensor, SensorMeasurement};
use Carbon\Carbon;

class ImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreImageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreImageRequest $request, Device $device)
    {
        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(storage_path('app/images'), $imageName);

        $image = Image::create([
            'device_id' => $device->id,
            'path' => storage_path('app/images') . '/' . $imageName,
        ]);

        SensorMeasurement::create([
            'device_id' => $device->id,
            'sensor_id' => Sensor::where('name', 'webcam')->first()->id,
            'value' => $image->id,
            'measured_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        return JsonResponseFactory::created();
    }
}
