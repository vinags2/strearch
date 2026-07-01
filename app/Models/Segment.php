<?php

namespace App\Models;

// use App\Traits\CommonModelFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;

// use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Segment extends Model
{
    use HasFactory;
    // use CommonModelFunctions, HasFactory;

    protected $guarded = [];

    protected $hidden = ['elevation_high', 'elevation_low', 'created_at', 'updated_at'];

    public function segmentEfforts(): hasMany
    {
        return $this->hasMany(SegmentEffort::class);
    }
}
