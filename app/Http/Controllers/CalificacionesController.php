<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResultadosFinales;
use App\Models\Proyectos;
use App\Models\Integrantes;
use App\Models\Evento;
use App\Models\Preguntas;
use App\Models\Calificacion;
use App\Models\Ganadores;
use App\Models\User;

class CalificacionesController extends Controller
{
    public function calificaciones()
    {
        return view('admin.calificaciones');
    }

    /* public function obtenerResultados(Request $request)
    {
        try {
            $eventoId = $request->query('evento_id');

            if (!$eventoId) {
                return response()->json(['message' => 'Debe proporcionar un ID de evento.'], 400);
            }

            $evento = Evento::find($eventoId);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado.'], 404);
            }

            $equiposIds = array_filter(explode(',', $evento->equiposParticipantes));
            $equipos = Proyectos::whereIn('id', $equiposIds)
                    ->whereNotIn('estado', [4, 6])
                    ->get();

            $equiposParticipantes = $equipos->map(function ($proyecto) {
                $integrantes = Integrantes::where('proyecto_id', $proyecto->id)->get();

                return [
                    'id' => $proyecto->id,
                    'nombreEquipo' => $proyecto->nombreEquipo,
                    'imagen' => $proyecto->foto_equipo,
                    'integrantesEquipo' => $integrantes,
                ];
            });

            $juecesIds = array_filter(explode(',', $evento->juecesParticipantes));
            $juecesParticipantes = User::whereIn('id', $juecesIds)->get(['id', 'name']);

            $response = [
                'descripcion' => $evento->descripcion,
                'fechaInicio' => $evento->fechaInicio,
                'fechaFin' => $evento->fechaFin,
                'equiposParticipantes' => $equiposParticipantes,
                'juecesParticipantes' => $juecesParticipantes,
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    } */

    public function obtenerResultados(Request $request)
    {
        try {

            $eventoId = $request->query('evento_id');

            if (!$eventoId) {
                return response()->json(['message' => 'Debe proporcionar un ID de evento.'], 400);
            }

            $evento = Evento::find($eventoId);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado.'], 404);
            }

            $equiposIds = array_filter(explode(',', $evento->equiposParticipantes));
            $equipos = Proyectos::whereIn('id', $equiposIds)
                    ->whereNotIn('estado', [4, 6])
                    ->get();

            $juecesIds = array_filter(explode(',', $evento->juecesParticipantes));
            $juecesParticipantes = User::whereIn('id', $juecesIds)->get(['id', 'name']);

            $equiposParticipantes = $equipos->map(function ($proyecto) use ($eventoId, $juecesParticipantes) {

                $juecesCalificados = ResultadosFinales::where('id_proyecto', $proyecto->id)
                    ->where('id_evento', $eventoId)
                    ->pluck('id_juez')
                    ->toArray();

                $juecesConEstado = $juecesParticipantes->map(function ($juez) use ($juecesCalificados) {
                    return [
                        'id' => $juez->id,
                        'name' => $juez->name,
                        'califico' => in_array($juez->id, $juecesCalificados),
                    ];
                });

                $calificacionFinal = !empty($proyecto->calificacion_final_promedio) ? $proyecto->calificacion_final_promedio : null;

                return [
                    'id' => $proyecto->id,
                    'nombreEquipo' => $proyecto->nombreEquipo,
                    'imagen' => $proyecto->foto_equipo,
                    'integrantesEquipo' => Integrantes::where('proyecto_id', $proyecto->id)->get(),
                    'jueces' => $juecesConEstado,
                    'calificacionFinal' => $calificacionFinal,
                ];
            });


            $response = [
                'descripcion' => $evento->descripcion,
                'fechaInicio' => $evento->fechaInicio,
                'fechaFin' => $evento->fechaFin,
                'equiposParticipantes' => $equiposParticipantes,
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }


    public function obtenerEventosResultados()
    {
        try {
            $eventos = Evento::orderBy('created_at', 'desc')->get(['id', 'descripcion']);

            if ($eventos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron eventos']);
            }

            return response()->json(['eventos' => $eventos]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerJueces()
    {
        $jueces = User::role('juez')->get();

        return response()->json($jueces);
    }

    public function obtenerCalificaciones(Request $request)
    {
        $idJuez = $request->id_juez;
        $idProyecto = $request->id_proyecto;

        if (!$idJuez || !$idProyecto) {
            return response()->json([
                'message' => 'Faltan parámetros necesarios.',
                'calificaciones' => [],
                'calificacion_final' => null,
            ], 400);
        }

        $preguntasConCalificaciones = Preguntas::with(['calificaciones' => function ($query) use ($idJuez, $idProyecto) {
            $query->where('id_juez', $idJuez)
                ->where('id_proyecto', $idProyecto);
        }])->whereHas('calificaciones', function ($query) use ($idJuez, $idProyecto) {
            $query->where('id_juez', $idJuez)
                ->where('id_proyecto', $idProyecto);
        })->get();

        $calificacionFinal = ResultadosFinales::where('id_juez', $idJuez)
            ->where('id_proyecto', $idProyecto)
            ->value('calificacion_final');

        if ($preguntasConCalificaciones->isEmpty() && !$calificacionFinal) {
            return response()->json([
                'message' => 'El juez no ha calificado este proyecto.',
                'calificaciones' => [],
                'calificacion_final' => null,
            ]);
        }

        return response()->json([
            'calificaciones' => $preguntasConCalificaciones,
            'calificacion_final' => $calificacionFinal,
        ]);
    }

    public function obtenerCalificacionesPorJuez(Request $request)
    {
        try {
            $idProyecto = $request->query('id_proyecto');
            $idJuez = $request->query('id_juez');

            if (!$idProyecto || !$idJuez) {
                return response()->json(['message' => 'El campo id_proyecto y id_juez son obligatorios.'], 400);
            }

            $calificaciones = Calificacion::where('id_juez', $idJuez)
            ->where('id_proyecto', $idProyecto)
            ->get(['id_pregunta', 'calificacion_pregunta'])
            ->map(function ($calificacion) {
                $pregunta = Preguntas::find($calificacion->id_pregunta);
                return [
                    'id_pregunta' => $calificacion->id_pregunta,
                    'pregunta' => $pregunta ? $pregunta->pregunta : null,
                    'calificacion_pregunta' => $calificacion->calificacion_pregunta,
                ];
            });

            $resultadoFinal = ResultadosFinales::where('id_proyecto', $idProyecto)
                ->where('id_juez', $idJuez)
                ->select('calificacion_final', 'comentario')
                ->first();

            return response()->json([
                'calificaciones' => $calificaciones,
                'resultado_final' => $resultadoFinal
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function publicarGanadores(Request $request)
    {
        try {

            $eventoId = $request->input('evento_id');

            if (!$eventoId) {
                return response()->json(['message' => 'Debe proporcionar un ID de evento.'], 400);
            }

            $evento = Evento::find($eventoId);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado.'], 404);
            }

            if ($evento->ganadores_publicados == 1) {
                return response()->json(['message' => 'Los ganadores ya se encuentran publicados.'], 400);
            }

            if ($evento->caso_empate == 1) {
                return response()->json([
                    'message' => 'No se pueden publicar los ganadores hasta resolver el empate.',
                ], 400);
            }

            $juecesIds = array_filter(explode(',', $evento->juecesParticipantes));
            $equiposIds = array_filter(explode(',', $evento->equiposParticipantes));
            $equiposIdsValidos = Proyectos::whereIn('id', $equiposIds)
                ->where('estado', 3)
                ->pluck('id')
                ->toArray();

            if (empty($equiposIdsValidos)) {
                return response()->json(['message' => 'No hay equipos válidos para participar en los ganadores.'], 400);
            }


            foreach ($juecesIds as $juezId) {
                foreach ($equiposIdsValidos as $equipoId) {

                    $resultado = ResultadosFinales::where('id_juez', $juezId)
                        ->where('id_proyecto', $equipoId)
                        ->where('id_evento', $eventoId)
                        ->first();

                    if (!$resultado) {
                        return response()->json(['message' => 'Existen jueces que no han calificado equipos.'], 400);
                    }
                }
            }

            //dd($resultado);

            $this->calcularCalificacionesPromedio($equiposIds, $eventoId);

            $hayEmpate = $this->verificarEmpate($eventoId);

            if ($hayEmpate) {
                $evento->caso_empate = 1;
                $evento->save();

                return response()->json([
                    'message' => 'Se detectó un empate en las calificaciones. No se pueden publicar los ganadores hasta resolver el empate.',
                ], 400);
            }

            $this->calcularGanadores($eventoId);

            $evento->ganadores_publicados = 1;
            $evento->save();

            return response()->json(['message' => 'Ganadores publicados exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al publicar los ganadores: ' . $e->getMessage()], 500);
        }
    }

    public function calcularCalificacionesPromedio(array $equiposIds, int $eventoId)
    {
        foreach ($equiposIds as $equipoId) {
            $calificaciones = ResultadosFinales::where('id_proyecto', $equipoId)
                ->where('id_evento', $eventoId)
                ->pluck('calificacion_final');

            if ($calificaciones->isNotEmpty()) {
                $promedio = $calificaciones->sum() / $calificaciones->count();

                Proyectos::where('id', $equipoId)->update(['calificacion_final_promedio' => $promedio]);
            }
        }
    }

    public function verificarEmpate($eventoId)
    {
        try {
            $evento = Evento::find($eventoId);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado.'], 404);
            }

            $proyectosParticipantes = Proyectos::whereNotNull('calificacion_final_promedio')
                ->whereIn('id', explode(',', $evento->equiposParticipantes))
                ->orderBy('calificacion_final_promedio', 'desc')
                ->get();

            $calificacionesTop3 = $proyectosParticipantes
                ->pluck('calificacion_final_promedio')
                ->map(fn($value) => (float) $value)
                ->unique()
                ->take(3);

            $proyectosTop3 = $proyectosParticipantes->filter(function ($proyecto) use ($calificacionesTop3) {
                return $calificacionesTop3->contains($proyecto->calificacion_final_promedio);
            });

            foreach ($calificacionesTop3 as $calificacion) {
                $proyectosConMismaCalificacion = $proyectosTop3->where('calificacion_final_promedio', $calificacion);

                if ($proyectosConMismaCalificacion->count() > 1) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al verificar el empate: ' . $e->getMessage()], 500);
        }
    }

    public function calcularGanadores($eventoId)
    {
        try {
            $evento = Evento::find($eventoId);

            if (!$evento) {
                return response()->json(['message' => 'Evento no encontrado.'], 404);
            }

            $equiposIds = array_filter(explode(',', $evento->equiposParticipantes));

            if (empty($equiposIds)) {
                return response()->json(['message' => 'No hay equipos participantes en este evento.'], 400);
            }

            $proyectos = Proyectos::whereIn('id', $equiposIds)
                ->whereNotNull('calificacion_final_promedio')
                ->orderBy('calificacion_final_promedio', 'desc')
                ->take(3)
                ->get();

            if ($proyectos->isEmpty()) {
                return response()->json(['message' => 'No hay proyectos con calificaciones en este evento.'], 400);
            }

            foreach ($proyectos as $index => $proyecto) {
                Ganadores::create([
                    'id_proyecto' => $proyecto->id,
                    'id_evento' => $eventoId,
                    'posicion' => $index + 1,
                ]);
            }

            return response()->json([
                'message' => 'Ganadores calculados y registrados exitosamente en tabla Ganadores.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al calcular los ganadores: ' . $e->getMessage(),
            ], 500);
        }
    }
}
