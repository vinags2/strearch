<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('filtered_activities', function (Blueprint $table) {
            $table->id()
                ->comment('Strava activity id');
            $table->string('name')
                ->nullable();
            $table->double('distance')
                ->nullable();
            $table->unsignedInteger('moving_time')
                ->comment('Time in seconds')
                ->nullable();
            $table->double('total_elevation_gain')
                ->nullable();
            $table->string('sport_type')
                ->comment('eg Ride')
                ->nullable();
            $table->dateTime('start_date_local')
                ->nullable()
                ->comment('eg 2024-04-29T15:42:43Z');
            $table->double('average_speed')
                ->comment('average speed in m/s. Convert to kph by multiplying by 3.6.')
                ->nullable();
            $table->double('max_speed')
                ->comment('max speed in m/s. Convert to kph by multiplying by 3.6.')
                ->nullable();
            $table->double('average_cadence')
                ->comment('average cadence')
                ->nullable();
            $table->double('average_watts')
                ->comment('average watts')
                ->nullable();
            $table->double('weighted_average_watts')
                ->comment('weighted average watts')
                ->nullable();
            $table->double('max_watts')
                ->comment('maximum watts')
                ->nullable();
            $table->double('kilojoules')
                ->comment('kilojoules')
                ->nullable();
            $table->double('average_heartrate')
                ->nullable()
                ->comment('average heartrate')
                ->default(0);
            $table->double('max_heartrate')
                ->nullable()
                ->comment('max heartrate')
                ->default(0);
            $table->double('suffer_score')
                ->nullable()
                ->comment('suffer score')
                ->default(0);
            $table->string('device_name')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
