<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Exception;

class CategoryController extends Controller
{

    public function categories()
    {
        return view('admin.evento');
    }

    public function get()
    {
        try {
            Log::info('CategoryController@get');
            $category = Category::orderBy('description')->get();
            
            if ($category->isEmpty()) {
                return response()->json(['message' => 'No hay data']);
            }
            Log::info('CategoryController@get - category: ' . json_encode($category));
            return response()->json(['data' => $category]);
        } catch (\Exception $e) {
            Log::error('CategoryController@get - error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
