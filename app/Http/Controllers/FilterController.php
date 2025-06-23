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

        return response()->json(['status' => 'Saving was successful'], 201);
    }

    public function store_all(Request $request)
    {
        foreach ($request->all() as $index => $oneRequest) {
            Filter::updateOrCreate(
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
        Filter::destroy($id);
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

    private function saveFilter($request)
    {
        Filter::set_all_to_inactive();
        Filter::updateOrCreate(
            ['name' => $request->input('name')],
            ['active' => $request->input('active'), 'filter' => $request->input('filters'), 'user_id' => $this->my('id'), 'name' => $request->input('name')]
        );
    }

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
