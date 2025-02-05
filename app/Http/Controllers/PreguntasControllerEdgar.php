<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Preguntas;
use App\Models\Respuestas;

class PreguntasController extends Controller
{
    public function preguntas()
    {
        return view('admin.preguntas');
    }

    public function obtenerPreguntasConRespuestas()
    {
     $preguntasConRespuestas = Preguntas::with('respuestas')->get();
     return response()->json($preguntasConRespuestas);
    }

    public function guardarPregunta(Request $request)
    {
        $validated = $request->validate([
            'pregunta' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'respuestas' => 'required|array',
            'respuestas.*.respuesta' => 'required|string|max:255',
            'respuestas.*.valor' => 'required|numeric',
        ]);

        $pregunta = Preguntas::create([
            'pregunta' => $validated['pregunta'],
            'valor' => $validated['valor'],
            'estado' => 0,
        ]);

        $respuestas = collect($validated['respuestas'])->map(function ($respuesta) use ($pregunta) {
            return [
                'respuesta' => $respuesta['respuesta'],
                'valor' => $respuesta['valor'],
                'id_pregunta' => $pregunta->id, 
            ];
        });

        Respuestas::insert($respuestas->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Pregunta y respuestas agregadas exitosamente.',
        ]);
    } 

    public function eliminarPreguntaTotal($id)
    {
        // Buscar la pregunta con sus respuestas asociadas
        $pregunta = Preguntas::with('respuestas')->find($id);

        if ($pregunta) {
            // Eliminar la pregunta junto con sus respuestas
            $pregunta->respuestas()->delete(); // Elimina todas las respuestas asociadas
            $pregunta->delete(); // Elimina la pregunta
            return response()->json(['success' => 'Pregunta y sus respuestas eliminadas correctamente.'], 200);
        } else {
            return response()->json(['error' => 'Pregunta no encontrada.'], 404);
        }
    }

    public function detallePreguntaTotal($id)
    {
        try {
            $pregunta = Preguntas::with('respuestas')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'pregunta' => $pregunta->pregunta,
                    'valor' => $pregunta->valor,
                    'respuestas' => $pregunta->respuestas
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function editarPregunta(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:preguntas,id',
                'pregunta' => 'required|string|max:255',
                'valor' => 'required|numeric',
                'respuestas' => 'required|array',
                //'respuestas.*.id' => 'nullable|integer',
                'respuestas' => 'required|array',
                'respuestas.*.respuesta' => 'required|string|max:255',
                'respuestas.*.valor' => 'required|numeric',
                'respuestasEliminadas' => 'array',
            ]);

            $pregunta = Preguntas::findOrFail($validated['id']);
            //dd($pregunta);
            $pregunta->update([
                'pregunta' => $validated['pregunta'],
                'valor' => $validated['valor'],
            ]);

            // Eliminar respuestas que ya no están
            if (!empty($validated['respuestasEliminadas'])) {
                Respuestas::whereIn('id', $validated['respuestasEliminadas'])->delete();
            }

            // foreach ($validated['respuestas'] as $respuestaData) {
            //     if (isset($respuestaData['id'])) {
            //         // Actualizar respuesta existente
            //         $respuesta = Respuestas::findOrFail($respuestaData['id']);
            //         $respuesta->update([
            //             'respuesta' => $respuestaData['respuesta'],
            //             'valor' => $respuestaData['valor'],
            //         ]);
            //     } else {
            //         // Crear nueva respuesta asociada a la pregunta
            //         Respuestas::create([
            //             'respuesta' => $respuestaData['respuesta'],
            //             'valor' => $respuestaData['valor'],
            //             'id_pregunta' => $pregunta->id,
            //         ]);
            //     }
            // }

            foreach ($validated['respuestas'] as $respuesta) {
                if (isset($respuesta['id'])) {
                    // Actualizar la respuesta existente
                    $respuestaExistente = Respuestas::find($respuesta['id']);
                    if ($respuestaExistente) {
                        $respuestaExistente->update([
                            'respuesta' => $respuesta['respuesta'],
                            'valor' => $respuesta['valor'],
                        ]);
                    }
                } else {
                    // Insertar una nueva respuesta
                    Respuestas::create([
                        'respuesta' => $respuesta['respuesta'],
                        'valor' => $respuesta['valor'],
                        'id_pregunta' => $pregunta->id,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pregunta y respuestas actualizadas exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function actualizarStatus($id, Request $request)
    {
        // Validar que el estado sea un valor booleano (0 o 1)
        $validated = $request->validate([
            'estado' => 'required|boolean',
        ]);

        // Buscar la pregunta por su ID
        $pregunta = Preguntas::find($id);

        if ($pregunta) {
            // Actualizar el estado de la pregunta
            $pregunta->estado = $validated['estado'];
            $pregunta->save();  // Guardar el cambio en la base de datos

            return response()->json([
                'success' => true,
                'message' => 'Estado de la pregunta actualizado exitosamente',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pregunta no encontrada',
            ], 404);
        }
    }

    public function updateRespuestas(Request $request, $id)
    {
        $respuesta = Respuestas::findOrFail($id);
        $respuesta->update($request->only(['respuesta', 'valor']));

        return response()->json(['message' => 'Respuesta actualizada correctamente']);
    }

     public function updatePreguntas(Request $request, $id)
    {
        $pregunta = Preguntas::findOrFail($id);
        $pregunta->update($request->only(['pregunta', 'valor']));
    
        return response()->json(['message' => 'Pregunta actualizada correctamente']);
    }
}
