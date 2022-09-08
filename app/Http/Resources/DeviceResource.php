<?php

namespace App\Http\Resources;

use App\Models\Device;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function __construct(Device $device)
    {
        $this->resource = $device;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'location' => new LocationResource($this->location),
            'sensors' => SensorResource::collection($this->sensors),
        ];
    }
}
