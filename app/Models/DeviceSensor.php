<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\DeviceSensor
 *
 * @property int $id
 * @property int $device_id
 * @property int $sensor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor whereSensorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceSensor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeviceSensor extends Pivot
{
    use HasFactory;

    protected $table = 'device_has_sensors';

    protected $fillable = [
        'device_id',
        'sensor_id',
    ];

    public static function getModelTable(): string
    {
        return with(new static)->getTable();
    }
}
