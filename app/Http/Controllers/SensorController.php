<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Requests\{StoreSensorRequest, UpdateSensorRequest};
use App\Http\Resources\SensorResource;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $results = SensorResource::collection(Sensor::simplePaginate(10));

        return JsonResponseFactory::success($results->response()->getData(true));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSensorRequest $request): JsonResponse
    {
        $sensor = Sensor::create([
            'name' => $request->validated('name'),
        ]);

        return JsonResponseFactory::created(new SensorResource($sensor));
    }

    /**
     * Display the specified resource.
     */
    public function show(Sensor $sensor): JsonResponse
    {
        return JsonResponseFactory::success(new SensorResource($sensor));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSensorRequest $request, Sensor $sensor): JsonResponse
    {
        if ($name = $request->validated('name')) {
            $sensor->name = $name;
        }

        $sensor->touch();
        $sensor->save();

        return JsonResponseFactory::success(new SensorResource($sensor));
    }
}
