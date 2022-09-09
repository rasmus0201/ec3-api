<?php

namespace App\Http\Resources;

use App\Models\Sensor;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorResource extends JsonResource
{
    public function __construct(Sensor $sensor)
    {
        $this->resource = $sensor;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
