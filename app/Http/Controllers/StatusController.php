<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use App\Models\Proyectos;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{

    public function status()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permisos', []);

        return view('admin.carreras', [
            'rolesGranted' => $roles,
            'permisosGranted' => $permisos,
        ]);
    }

    public function get()
    {
        try {
            Log::info('StatusController@get');
            $status = Status::orderBy('name')->get();

            if ($status->isEmpty()) {
                return response()->json(['message' => 'No hay data']);
            }
            Log::info('StatusController@get - status: ' . json_encode($status));
            return response()->json(['data' => $status]);
        } catch (\Exception $e) {
            Log::error('StatusController@get - error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
