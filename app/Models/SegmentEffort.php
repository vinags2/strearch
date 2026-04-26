<?php

namespace App\Models;

use App\Traits\CommonModelFunctions;
use Illuminate\Database\Eloquent\Model;

class SegmentEffort extends Model
{
    use CommonModelFunctions;

    protected $guarded = [];

    protected $appends = ['moving_time_as_string', 'start_date_local_as_timestamp'];

    protected $hidden = ['user_id', 'athlete_id',  'elapsed_time', 'device_watts', 'elevation_high', 'elevation_low', 'created_at', 'updated_at'];
}
