<?php

namespace App\Models;

use App\Traits\CommonModelFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SegmentEffort extends Model
{
    use CommonModelFunctions, HasFactory;

    protected $guarded = [];

    protected $appends = ['moving_time_as_string', 'start_date_as_timestamp', 'start_time', 'formatted_start_date_local', 'activity_name'];

    protected $hidden = ['user_id', 'athlete_id',  'elapsed_time', 'device_watts', 'elevation_high', 'elevation_low', 'created_at', 'updated_at'];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function getActivityNameAttribute()
    {
        return $this->activity->name;
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }
}
