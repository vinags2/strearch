<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Setting;
use App\Traits\Utilities;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilterController extends Controller
{
    use Utilities;

    // private $type_of_name_search;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $filters = $this->my('filters');

        return Inertia::render('Filter', [
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created filter
     */
    public function store(Request $request)
    {
        $this->saveFilter($request);

        // switch ($request->input('button_pressed')) {
        //     case 'delete':
        //         $this->delete();
        //         break;
        //     case 'saveasnew':
        //         $this->saveNewFilter($request);
        //         break;
        //     default: // save existing
        //         $this->saveFilter($request);
        // }

        return response()->json(['status' => 'Saving was successful'], 201);

        // return $this->index();
    }

    /**
     * Store an array of filters
     */
    // public function store_all(Request $request)
    // {
    //     $filters = urldecode(substr($request->getQueryString(), 8));
    //     // $filters = urldecode($request->getQueryString());
    //     // $filters = $request->input('filters');
    //     // $filters = urldecode($request->getQueryString());
    //     // $filters = json_decode(urldecode($request->getQueryString()));
    //     // $filters = json_decode(urldecode($request->input()));
    //     // $filters = json_encode($request->all());
    //     // $filters = $request->all();
    //     Filter::updateOrCreate(
    //         ['name' => 'paramater passed to URL'],
    //         [
    //             'name' => 'paramater passed to URL',
    //             'filter' => $filters,
    //             // 'filter' => urldecode($request->getQueryString()),
    //             'active' => 0,
    //             'user_id' => $this->my('id'),
    //         ]);

    //     return response()->json(['status' => 'Saved filters successfully'], 201);

    //     // return response()->json(['status' => $filters], 500);
    //     // $filters = json_decode($filters);
    //     foreach ($filters as $filter) {
    //         Filter::updateOrCreate(
    //             // ['id' => 1],
    //             ['name' => $request->input('filterName')],
    //             ['active' => $filter->active, 'filter' => $filter->filter, 'user_id' => $this->my('id'), 'name' => $filter->name]
    //         );
    //     }

    //     return response()->json(['status' => 'Saving was successful'], 201);

    // }

    public function store_all(Request $request)
    {
        foreach ($request->all() as $index => $oneRequest) {
            Filter::updateOrCreate(
                // ['id' => 1],
                ['name' => $request->input($index.'.'.'filterName')],
                ['active' => $request->input($index.'.'.'active'),
                    'filter' => $request->input($index.'.'.'filters'),
                    'user_id' => $this->my('id'),
                    'name' => $request->input($index.'.'.'filterName'),
                ]
            );
        }

        return response()->json(['status' => 'Saving was successful'], 201);

    }

    public function delete($id)
    {
        // $id = request()->input('id');
        // $activeFilter = Filter::active_filter_as_collection();
        Filter::destroy($id);
        // if ($activeFilter->id == $id) {
        //     $activeFilter = Filter::first();
        //     $activeFilter->active = 1;
        //     $activeFilter->save();
        // }
    }

    public function setActiveFilter($id)
    {
        Filter::set_all_to_inactive();
        if ($id >= 0) {
            $filter = Filter::find($id);
            $filter->active = 1;
            $filter->save();
        }
    }

    private function saveNewFilter($request)
    {
        Filter::set_all_to_inactive();
        $filter = $this->createFilter($request);
        $newFilter = new Filter;
        $newFilter->fill([
            'active' => 1,
            'filter' => $filter,
            'user_id' => $this->my('id'),
            'name' => $request->input('name'),
        ]);
        $newFilter->save();
    }

    private function saveFilter($request)
    {
        Filter::set_all_to_inactive();
        // $filter = $this->createFilter($request);
        Filter::updateOrCreate(
            // ['id' => 1],
            ['name' => $request->input('filterName')],
            ['active' => $request->input('active'), 'filter' => $request->input('filters'), 'user_id' => $this->my('id'), 'name' => $request->input('filterName')]
        );
    }

    // private function createFilter($request)
    // {
    //     $filter = [];
    //     if ($request->input('distance_lessthan') && $request->input('distance_lessthan_number')) {
    //         $filter[] = ['distance', '<', $request->input('distance_lessthan_number') * 1000];
    //     }
    //     if ($request->input('distance_greaterthan') && $request->input('distance_greaterthan_number')) {
    //         $filter[] = ['distance', '>', $request->input('distance_greaterthan_number') * 1000];
    //     }
    //     if ($request->input('elevation_lessthan') && $request->input('elevation_lessthan_number')) {
    //         $filter[] = ['total_elevation_gain', '<', $request->input('elevation_lessthan_number')];
    //     }
    //     if ($request->input('elevation_greaterthan') && $request->input('elevation_greaterthan_number')) {
    //         $filter[] = ['total_elevation_gain', '>', $request->input('elevation_greaterthan_number')];
    //     }
    //     if ($request->input('heartrate_lessthan') && $request->input('heartrate_lessthan_number')) {
    //         $filter[] = ['average_heartrate', '<', $request->input('heartrate_lessthan_number')];
    //     }
    //     if ($request->input('heartrate_greaterthan') && $request->input('heartrate_greaterthan_number')) {
    //         $filter[] = ['average_heartrate', '>', $request->input('heartrate_greaterthan_number')];
    //     }
    //     if ($request->input('max_heartrate_lessthan') && $request->input('max_heartrate_lessthan_number')) {
    //         $filter[] = ['max_heartrate', '<', $request->input('max_heartrate_lessthan_number')];
    //     }
    //     if ($request->input('max_heartrate_greaterthan') && $request->input('max_heartrate_greaterthan_number')) {
    //         $filter[] = ['max_heartrate', '>', $request->input('max_heartrate_greaterthan_number')];
    //     }

    //     if ($request->input('speed_lessthan') && $request->input('speed_lessthan_number')) {
    //         $filter[] = ['average_speed', '<', $request->input('speed_lessthan_number') / 3.6];
    //     }
    //     if ($request->input('speed_greaterthan') && $request->input('speed_greaterthan_number')) {
    //         $filter[] = ['average_speed', '>', $request->input('speed_greaterthan_number') / 3.6];
    //     }
    //     if ($request->input('effort_lessthan') && $request->input('effort_lessthan_number')) {
    //         $filter[] = ['suffer_score', '<', $request->input('effort_lessthan_number')];
    //     }
    //     if ($request->input('effort_greaterthan') && $request->input('effort_greaterthan_number')) {
    //         $filter[] = ['suffer_score', '>', $request->input('effort_greaterthan_number')];
    //     }
    //     if ($request->input('cadence_lessthan') && $request->input('cadence_lessthan_number')) {
    //         $filter[] = ['average_cadence', '<', $request->input('cadence_lessthan_number')];
    //     }
    //     if ($request->input('cadence_greaterthan') && $request->input('cadence_greaterthan_number')) {
    //         $filter[] = ['average_cadence', '>', $request->input('cadence_greaterthan_number')];
    //     }
    //     if ($request->input('watts_lessthan') && $request->input('watts_lessthan_number')) {
    //         $filter[] = ['average_watts', '<', $request->input('watts_lessthan_number')];
    //     }
    //     if ($request->input('watts_greaterthan') && $request->input('watts_greaterthan_number')) {
    //         $filter[] = ['average_watts', '>', $request->input('watts_greaterthan_number')];
    //     }
    //     if ($request->input('time_lessthan') && $request->input('time_lessthan_number')) {
    //         $filter[] = ['moving_time', '<', $this->convertHoursMinutesToSeconds($request->input('time_lessthan_number'))];
    //     }
    //     if ($request->input('time_greaterthan') && $request->input('time_greaterthan_number')) {
    //         $filter[] = ['moving_time', '>', $this->convertHoursMinutesToSeconds($request->input('time_greaterthan_number'))];
    //     }
    //     if ($request->input('date_lessthan') && $request->input('date_lessthan_number')) {
    //         $filter[] = ['start_date_local', '<', $request->input('date_lessthan_number')];
    //     }
    //     if ($request->input('date_greaterthan') && $request->input('date_greaterthan_number')) {
    //         $filter[] = ['start_date_local', '>', $request->input('date_greaterthan_number')];
    //     }
    //     if ($request->input('title') && $request->input('title_number')) {
    //         if ($request->input('search') == 1) {
    //             $filter[] = $this->formatExactPhraseSearch($request);
    //         } else {
    //             $filter = $this->formatAllWordSearch($request, $filter);
    //         }
    //     }
    //     $virtual = $request->input('virtual');
    //     if ($virtual != 4) {
    //         switch ($virtual) {
    //             case 0:
    //                 $val = 'VirtualRide';
    //                 break;
    //             case 1:
    //                 $val = 'VirtualWalk';
    //                 break;
    //             case 2:
    //                 $val = 'Ride';
    //                 break;
    //             case 3:
    //                 $val = 'Walk';
    //                 break;
    //             case 5:
    //                 $val = 'Workout';
    //                 break;
    //             default:
    //                 $val = 'dummy';
    //         }
    //         $filter[] = ['type', '=', $val];
    //     }

    //     return $filter;
    // }

    // private function convertHoursMinutesToSeconds($time)
    // {
    //     [$hours, $minutes] = explode(':', $time, 2);

    //     return $minutes * 60 + $hours * 3600;
    // }

    // private function formatExactPhraseSearch($request)
    // {
    //     $this->type_of_name_search = 1;

    //     return ['name', 'like', '%'.$request->input('title_number').'%'];
    // }

    // private function formatAllWordSearch($request, $filter)
    // {
    //     $this->type_of_name_search = 0;
    //     $words = preg_split('/\s+/', $request->input('title_number'));
    //     foreach ($words as $word) {
    //         $filter[] = ['name', 'like', '%'.$word.'%'];
    //     }

    //     return $filter;
    // }

    /**
     * Display the specified resource.
     */
    public function show(Filter $filter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Filter $filter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Filter $filter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Filter $filter)
    {
        //
    }

    public function api_get($filtered = null)
    {
        $filters = $this->my('filters');

        return response()->json([
            'filters' => $filters ?? false,
            'date_of_last_activities_strava_update' => Setting::last_activities_update_from_strava(),
        ],
            201
        );
    }
}
