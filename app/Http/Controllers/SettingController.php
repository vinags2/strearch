<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Traits\Utilities;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->NotDoneYet();
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

    public function saveDateOfActivitiesStore()
    {
        Setting::updateOrCreate(
            ['user_id' => $this->my('id')],
            ['last_activities_update_from_strava' => now()]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
