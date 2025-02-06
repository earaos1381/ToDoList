<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;

class ImagesController extends Controller
{

    public function Obtenerimagenes($tipo = null)
    {
        if ($tipo) {
            $images = Images::where('tipo', $tipo)->where('estado', 1)->get();
        } else {
            $images = Images::where('estado', 1)->get();
        }

        return response()->json($images);
    }
    
}
