<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Sensor
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SensorMeasurement[] $sensorMeasurement
 * @property-read int|null $sensor_measurement_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sensor whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SensorMeasurement[] $SensorMeasurements
 * @property-read int|null $sensor_measurements_count
 */
class Sensor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function SensorMeasurements()
    {
        return $this->hasMany(SensorMeasurement::class);
    }
}
