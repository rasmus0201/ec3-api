<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SensorDataOld extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sensor_data_old';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id', 'value', 'sensored_at',
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
}
