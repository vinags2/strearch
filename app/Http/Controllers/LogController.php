<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function api_log($data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        Log::channel('gsv')->info($data);

        return response()->json(['status' => 'Logging was successful'], 201);

    }
}
