<?php

namespace App\Http\Controllers;

use App\Models\FilteredActivity;
use App\Traits\Utilities;
use Illuminate\Http\Request;

class FilteredActivityController extends Controller
{
    use Utilities;

    private $sort_column;

    private $sort_direction;

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
     * Store an array of activities from an API call
     */
    public function store(Request $request)
    {
        FilteredActivity::truncate();

        foreach ($request->input() as $activity) {
            $this->store_one_activity($activity);
        }

        return response()->json(['status' => 'Saving was successful'], 201);
    }

    private function store_one_activity($activity)
    {
        // $r = FilteredActivity::updateOrCreate(
        //     ['id' => $activity['id']],
        $r = FilteredActivity::updateOrCreate(
            [
                'id' => $activity['id'], ],
            [
                'name' => $activity['name'],
                'distance' => $activity['distance'] ?? null,
                'total_elevation_gain' => $activity['total_elevation_gain'] ?? null,
                'sport_type' => $activity['sport_type'] ?? null,
                'start_date_local' => $activity['start_date_local'] ?? null,
                'average_speed' => $activity['average_speed'] ?? null,
                'max_speed' => $activity['max_speed'] ?? null,
                'average_cadence' => $activity['average_cadence'] ?? null,
                'average_watts' => $activity['average_watts'] ?? null,
                'weighted_average_watts' => $activity['weighted_average_watts'] ?? null,
                'max_watts' => $activity['max_watts'] ?? null,
                'kilojoules' => $activity['kilojoules'] ?? null,
                'average_heartrate' => $activity['average_heartrate'] ?? null,
                'max_heartrate' => $activity['max_heartrate'] ?? null,
                'suffer_score' => $activity['suffer_score'] ?? null,
                'moving_time' => $activity['moving_time'] ?? null,
                'device_name' => $activity['device_name'] ?? null,
                'user_id' => auth()->user()->id,
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(FilteredActivity $FilteredActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FilteredActivity $FilteredActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FilteredActivity $FilteredActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FilteredActivity $FilteredActivity)
    {
        //
    }
}
