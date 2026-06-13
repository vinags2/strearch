<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Traits\Utilities;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('settings/Preferences', [
            'autoUpdateActivities' => Setting::auto_update_activities(),
            'showViewInStravaAsText' => Setting::show_viewinstrava_as_text(),
            'updateDeviceForBiketerra' => Setting::update_device_for_biketerra(),
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
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Preferences', [
            'autoUpdateActivities' => Setting::auto_update_activities(),
            'showViewInStravaAsText' => Setting::show_viewinstrava_as_text(),
            'updateDeviceForBiketerra' => Setting::update_device_for_biketerra(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request->input(), $request->input('autoUpdateActivities'));

        Setting::updateOrCreate(
            ['user_id' => $this->my('id')],
            ['auto_update_activities' => $request->input('autoUpdateActivities'),
                'show_viewinstrava_as_text' => $request->input('showViewInStravaAsText'),
                'update_device_for_biketerra' => $request->input('updateDeviceForBiketerra')]
        );

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
