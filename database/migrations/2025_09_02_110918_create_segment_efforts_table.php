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
        Schema::create('segment_efforts', function (Blueprint $table) {
            $table->id()
                ->comment('Strava segment id');
            $table->unsignedBigInteger('activity_id')
                ->index();
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->unsignedInteger('elapsed_time')
                ->comment('Time in seconds')
                ->nullable();
            $table->dateTime('start_date')
                ->nullable()
                ->index();
            $table->dateTime('start_date_local')
                ->nullable()
                ->index()
                ->comment('eg 2024-04-29T15:42:43Z');
            $table->double('distance')
                ->nullable()
                ->index();
            $table->boolean('is_kom')
                ->nullable()
                ->default(false);
            $table->string('name')
                ->nullable()
                ->index();
            $table->unsignedBigInteger('athlete_id')
                ->comment('linked to Athletes Id')
                ->index();
            $table->unsignedInteger('moving_time')
                ->comment('Time in seconds')
                ->nullable()
                ->index();
            $table->double('average_cadence')
                ->comment('average cadence')
                ->nullable();
            $table->double('average_watts')
                ->comment('average watts')
                ->nullable();
            $table->double('weighted_average_watts')
                ->comment('weighted average watts')
                ->nullable();
            $table->boolean('device_watts')
                ->nullable()
                ->comment('is wattage recorded from a power metre?')
                ->default(false);
            $table->double('average_heartrate')
                ->nullable()
                ->comment('average heartrate')
                ->default(0);
            $table->double('max_heartrate')
                ->nullable()
                ->comment('max heartrate')
                ->default(0);
            $table->unsignedBigInteger('kom_rank')
                ->comment('The rank of the effort on the global leaderboard if it belongs in the top 10 at the time of upload')
                ->nullable();
            $table->unsignedBigInteger('pr_rank')
                ->comment('The rank of the effort on the athlete\'s leaderboard if it belongs in the top 3 at the time of upload')
                ->nullable();
            $table->string('activity_type')
                ->comment('May take one of the following values: Ride, Run')
                ->default('Ride')
                ->nullable();
            $table->double('average_grade')
                ->nullable();
            $table->double('maximum_grade')
                ->nullable();
            $table->double('elevation_high')
                ->comment('The segments\'s highest elevation, in meters')
                ->nullable();
            $table->double('elevation_low')
                ->comment('The segments\'s lowest elevation, in meters')
                ->nullable();
            $table->unsignedBigInteger('climb_category')
                ->comment('The category of the climb [0, 5]. Higher is harder ie. 5 is Hors catégorie, 0 is uncategorized in climb_category.')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_efforts');
    }
};
