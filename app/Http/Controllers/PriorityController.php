<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Priority;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class PriorityController extends Controller
{

    public function priorities()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permisos', []);

        return view('admin.universidades', [
            'rolesGranted' => $roles,
            'permisosGranted' => $permisos,
        ]);
    }

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
