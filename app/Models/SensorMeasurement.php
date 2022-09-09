<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SensorMeasurement
 *
 * @property int $id
 * @property int $device_id
 * @property int $sensor_id
 * @property float $value
 * @property \Illuminate\Support\Carbon $measured_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\Sensor|null $sensor
 * @method static \Database\Factories\SensorMeasurementFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement query()
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereSensorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereSensoredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereValue($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|SensorMeasurement whereMeasuredAt($value)
 */
class SensorMeasurement extends Model
{
    use HasFactory;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id', 'value', 'measured_at', 'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'measured_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
