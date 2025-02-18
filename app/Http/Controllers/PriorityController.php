<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Priority;
use Exception;
use Illuminate\Support\Facades\Log;

class PriorityController extends Controller
{

    public function get()
    {
        try {
            Log::info('PriorityController@get');
            $priority = Priority::orderBy('description')->get();

            if ($priority->isEmpty()) {
                return response()->json(['message' => 'No Data']);
            }
            Log::info('PriorityController@get - priority: ' . json_encode($priority));
            return response()->json(['data' => $priority]);
        } catch (\Exception $e) {
            Log::error('PriorityController@get - error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
