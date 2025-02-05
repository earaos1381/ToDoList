<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Proyectos;

class FotoController extends Controller
{
    public function upload(Request $request)
    {

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png|max:5120',
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        $file = $request->file('foto');
        $path = $file->store('fotos', 'public');

        $proyecto = Proyectos::find($request->proyecto_id);
        $proyecto->foto_equipo = "/storage/$path";
        $proyecto->save();

        return response()->json(['path' => $proyecto->foto_equipo], 200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id', // asegura que el proyecto existe
        ]);

        $proyecto = Proyectos::find($request->proyecto_id);

        if ($proyecto && $proyecto->foto_equipo) {
            $path = str_replace('/storage/', '', $proyecto->foto_equipo);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path); // elimina la imagen

                // Restablece el campo 'foto_equipo' a null o una imagen por defecto
                $proyecto->foto_equipo = null;
                $proyecto->save();

                return response()->json(['message' => 'Foto eliminada con éxito.'], 200);
            }
        }

        return response()->json(['error' => 'No se pudo eliminar la imagen.'], 400);
    }
}

