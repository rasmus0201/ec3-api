<?php

namespace App\Http\Controllers;

use App\Http\JsonResponseFactory;
use App\Http\Requests\{StoreLocationRequest, UpdateLocationRequest};
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $results = LocationResource::collection(Location::simplePaginate(10));

        return JsonResponseFactory::success($results->response()->getData(true));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = Location::create([
            'name' => $request->validated('name'),
            'timezone' => $request->validated('timezone'),
            'lat' => $request->validated('lat'),
            'long' => $request->validated('long'),
        ]);

        return JsonResponseFactory::created(new LocationResource($location));
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): JsonResponse
    {
        return JsonResponseFactory::success(new LocationResource($location));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        if ($name = $request->validated('name')) {
            $location->name = $name;
        }

        if ($lat = $request->validated('lat')) {
            $location->lat = $lat;
        }

        if ($long = $request->validated('long')) {
            $location->long = $long;
        }

        if ($timezone = $request->validated('timezone')) {
            $location->timezone = $timezone;
        }

        $location->touch();
        $location->save();

        return JsonResponseFactory::success(new LocationResource($location));
    }
}
