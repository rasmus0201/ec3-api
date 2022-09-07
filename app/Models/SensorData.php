<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensorData extends Model
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
        'sensor_id', 'value', 'sensored_at', 'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sensored_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
