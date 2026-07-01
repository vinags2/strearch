<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use App\Traits\Utilities;
use Illuminate\Http\Request;

// use Inertia\Inertia;
// use Inertia\Response;

class SegmentController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function store_one_segment($segment_id)
    {

        if (! $this->segmentExists($segment_id)) {
            // Fetch segment data from Strava API
            $segment_data = (new GetDataFromStravaController)->getASegment($segment_id);

            // Store the segment data in the database
            $r = Segment::updateOrCreate(
                ['id' => $segment_data['id']],
                [
                    'id' => $segment_data['id'],
                    'name' => $segment_data['name'],
                    'activity_type' => $segment_data['activity_type'],
                    'distance' => $segment_data['distance'],
                    'average_grade' => $segment_data['average_grade'],
                    'maximum_grade' => $segment_data['maximum_grade'],
                    'elevation_high' => $segment_data['elevation_high'],
                    'elevation_low' => $segment_data['elevation_low'],
                    'elevation_profile' => $segment_data['elevation_profile'],
                    'is_private' => $segment_data['private'],
                    'is_hazardous' => $segment_data['hazardous'],
                    'is_starred' => $segment_data['starred'],
                    'total_elevation_gain' => $segment_data['total_elevation_gain'],
                    'map_id' => $segment_data['map']['id'],
                    'map_polyline' => $segment_data['map']['polyline'],
                ]
            );

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Segment $segment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Segment $segment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Segment $segment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Segment $segment)
    {
        //
    }

    public function segmentExists($segment_id)
    {
        return Segment::where('id', $segment_id)->exists();

    }
}
