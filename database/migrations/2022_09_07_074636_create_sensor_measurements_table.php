<?php

use App\Models\{Device, Sensor};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Device::class);
            $table->foreignIdFor(Sensor::class);
            $table->double('value', 8, 2);
            $table->timestamp('measured_at', 6);
            $table->timestamp('created_at', 6)->nullable();

            $table->index(['device_id', 'sensor_id', 'measured_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_measurements');
    }
};
