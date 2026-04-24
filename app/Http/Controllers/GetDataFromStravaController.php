<?php

namespace App\Http\Controllers;

use App\Traits\Utilities;
use Illuminate\Support\Facades\Http;

class GetDataFromStravaController extends Controller
{
    use Utilities;

    private $access_token;

    public function getAnActivity($activity_id)
    {
        $this->access_token = $this->getAccessToken();
        $this->authoriseWithStrava();

        $activity_url = $this->getActivityURL();
        $full_url = $activity_url.'/'.$activity_id.'?access_token='.$this->access_token;

        $ret = Http::get($full_url);
        // dd($ret->failed(), $ret);
        $retJson = $ret->json();
        // dd($retJson);
        dd($retJson['segment_efforts'], $retJson['segment_efforts'][1]['name'], $retJson);

    }

    private function authoriseWithStrava()
    {

        $auth_url = $this->getAuthURL();
        $client_id = $this->getClientId();
        $client_secret = $this->getClientSecret();
        $refresh_token = $this->getRefreshToken();

        $full_url = $auth_url.'?client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=refresh_token&refresh_token='.$refresh_token;

        $ret = Http::post($full_url);

        if ($ret->failed()) {
            // Log error or handle failure
            return null;
        }

        $this->access_token = $ret->json()['access_token'];

        (new StravaController)->store($this->access_token, $ret->json()['refresh_token']);
        // return $response->json();
    }
}
