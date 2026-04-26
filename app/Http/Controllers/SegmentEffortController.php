<?php

namespace App\Http\Controllers;

use App\Models\SegmentEffort;
use App\Models\Setting;
use App\Traits\Utilities;
use Inertia\Inertia;
use Inertia\Response;

class SegmentEffortController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $segment_efforts = $this->getSegmentEfforts();

        return Inertia::render('SegmentEfforts', [
            'autoUpdateSegmentEfforts' => Setting::auto_update_activities(),
            'showViewInStravaAsText' => Setting::show_viewinstrava_as_text(),
        ]);

    }

    private function getSegmentEfforts()
    {
        $segment_efforts = $this->my('segment_efforts', false); // ->where($filter);
        // $segment_efforts = $this->addOrderBy($segment_efforts);

        return $segment_efforts->get();
        // return $activities->paginate(50);
    }

    // private function addOrderBy($segment_efforts)
    // {
    //     $sort_info = Setting::active_sort();

    //     $this->sort_column = $sort_info['sort_column'];
    //     $this->sort_direction = $sort_info['sort_direction'];

    //     return $segment_efforts->orderBy($sort_info['sort_column'], $sort_info['sort_direction']);
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store an array of segment_efforts from an API call
     */
    public function store()
    {
        $request = request();
        $segment_efforts = $request->input('segment_efforts' ?? []);
        foreach ($segment_efforts as $segment_effort) {
            $this->store_one_segment_effort($segment_effort);
        }

        // return response()->json(['status' => 'Saving was successful'], 201);
    }

    public function store_segment_effort(SegmentEffort $segment_effort)
    {
        $this->store_one_segment_effort(request()->all());

        return response()->json(['status' => 'Saving was successful'], 201);

    }

    private function store_one_segment_effort($segment_effort)
    {
        $r = SegmentEffort::updateOrCreate(
            ['id' => $segment_effort['id']],
            [
                'id' => $segment_effort['id'],
                'activity_id' => $segment_effort['activity']['id'],
                'athlete_id' => $segment_effort['athlete']['id'],
                'segment_id' => $segment_effort['segment']['id'],
                'user_id' => $this->my('id'),
                'elapsed_time' => $segment_effort['elapsed_time'] ?? null,
                'start_date' => $segment_effort['start_date'] ?? null,
                'start_date_local' => $segment_effort['start_date_local'] ?? null,
                'distance' => $segment_effort['distance'] ?? null,
                'is_kom' => $segment_effort['is_kom'] ?? null,
                'name' => $segment_effort['name'],
                'moving_time' => $segment_effort['moving_time'] ?? null,
                'average_cadence' => $segment_effort['average_cadence'] ?? null,
                'average_watts' => $segment_effort['average_watts'] ?? null,
                'weighted_average_watts' => $segment_effort['weighted_average_watts'] ?? null,
                'device_watts' => $segment_effort['device_watts'] ?? null,
                'average_heartrate' => $segment_effort['average_heartrate'] ?? null,
                'max_heartrate' => $segment_effort['max_heartrate'] ?? null,
                'kom_rank' => $segment_effort['kom_rank'] ?? null,
                'pr_rank' => $segment_effort['pr_rank'] ?? null,
                'activity_type' => $segment_effort['activity_type'] ?? null,
                'average_grade' => $segment_effort['average_grade'] ?? null,
                'maximum_grade' => $segment_effort['maximum_grade'] ?? null,
                'elevation_high' => $segment_effort['elevation_high'] ?? null,
                'elevation_low' => $segment_effort['elevation_low'] ?? null,
                'climb_category' => $segment_effort['climb_category'] ?? null,
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(SegmentEffort $segment_effort)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegmentEffort $segment_effort)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegmentEffort $segment_effort)
    {
        if ($segment_effort->delete() === false) {
            return redirect()->back()->withErrors([
                'errors' => 'Unable to delete the segment_effort',
            ]);
        }

        return redirect()->back();

    }

    public function api_get()
    {
        $segment_efforts = $this->getSegmentEfforts();

        return response()->json([
            'segment_efforts' => $segment_efforts ?? false,
            'date_of_last_activities_strava_update' => Setting::last_activities_update_from_strava(),
            'sportTypes' => $this->getSportTypes(),
            'deviceNames' => $this->getDeviceNames(),
        ],
            201
        );
    }
}
