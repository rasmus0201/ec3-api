<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function __construct(Location $location)
    {
        $this->resource = $location;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'timezone' => $this->timezone,
            'timezoneOffset' => $this->getTimezoneOffset(),
            'lat' => $this->lat,
            'long' => $this->long,
        ];
    }
}
