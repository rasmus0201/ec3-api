<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Requests\{StoreDeviceRequest, UpdateDeviceRequest};
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $results = DeviceResource::collection(Device::with(['location', 'sensors'])->simplePaginate(10));

        return JsonResponseFactory::success($results->response()->getData(true));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $device = Device::create([
            'name' => $request->validated('name'),
            'location_id' => $request->validated('location_id'),
        ]);

        if ($sensorIds = $request->validated('sensor_ids')) {
            $device->sensors()->attach($sensorIds);
        }

        return JsonResponseFactory::created(
            new DeviceResource($device->load(['location', 'sensors']))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device): JsonResponse
    {
        return JsonResponseFactory::success(new DeviceResource($device->load(['location', 'sensors'])));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device): JsonResponse
    {
        if ($name = $request->validated('name')) {
            $device->name = $name;
        }

        if ($locationId = $request->validated('location_id')) {
            $device->location_id = $locationId;
        }

        if ($sensorIds = $request->validated('sensor_ids')) {
            foreach ($sensorIds as $existingId => $value) {
                if ($value === null) {
                    $device->sensors()->detach($existingId);
                    continue;
                }

                $device->sensors()->attach($value);
            }
        }

        $device->touch();
        $device->save();

        return JsonResponseFactory::success(new DeviceResource($device->load(['location', 'sensors'])));
    }
}
