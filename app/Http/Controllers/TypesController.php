<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preguntas;
use App\Models\Respuestas;
use App\Models\TaskType;
use App\Models\Calificacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TypesController extends Controller
{
    public function types()
    {
        return view('admin.preguntas');
    }

    public function get()
    {
        try {
            Log::info('TypesController@get');
            $type = TaskType::orderBy('name')->get();

            if ($type->isEmpty()) {
                return response()->json(['message' => 'No hay data']);
            }
            Log::info('TypesController@get - type: ' . json_encode($type));
            return response()->json(['data' => $type]);
        } catch (\Exception $e) {
            Log::error('TypesController@get - error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
