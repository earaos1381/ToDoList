<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Ganadores;
use App\Models\Proyectos;
use App\Models\Integrantes;

use Exception;

class GanadoresController extends Controller
{
    public function ganadoresEvento()
    {
        return view('admin.ganadoresEvento');
    }

    /* public function obtenerGanadores($idEvento)
    {
        try {
            $evento = Evento::find($idEvento);

            if (!$evento) {
                return response()->json(['status' => 'error', 'messages' => ['No se encontró el evento especificado.']], 200);
            }

            $nombreEvento = $evento->descripcion;
            $anioEvento = $evento->created_at->format('Y');

            $ganadores = Ganadores::where('id_evento', $idEvento)->get();

            if ($ganadores->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'evento' => [
                        'nombre' => $nombreEvento,
                        'anio' => $anioEvento,
                    ],
                    'data' => []
                ], 200);
            }

            $resultados = $ganadores->map(function ($ganador) {
                $proyecto = Proyectos::find($ganador->id_proyecto);

                if (!$proyecto) {
                    return null;
                }

                $integrantes = Integrantes::where('proyecto_id', $proyecto->id)->get();
                $lider = $integrantes->firstWhere('id', $proyecto->nombreLider);

                $integrantesOrdenados = $lider
                    ? $integrantes->where('id', '!=', $lider->id)->prepend($lider)
                    : $integrantes;

                return [
                    'posicion' => $ganador->posicion,
                    'proyecto' => [
                        'id' => $proyecto->id,
                        'nombre' => $proyecto->nombreEquipo,
                        'descripcion' => $proyecto->temaProyecto,
                        'fotoEquipo' => $proyecto->foto_equipo,
                        'lider' => $lider ? $lider->nombre_integrante : null,
                        'integrantes' => $integrantesOrdenados->map(function ($integrante) {
                            return [
                                'id' => $integrante->id,
                                'nombre' => $integrante->nombre_integrante,
                            ];
                        }),
                    ],
                ];
            })->filter();

            return response()->json([
                'status' => 'success',
                'evento' => [
                    'nombre' => $nombreEvento,
                    'anio' => $anioEvento,
                ],
                'data' => $resultados,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'warning', 'messages' => [$e->getMessage()]], 200);
        }
    } */

    public function obtenerGanadores($idEvento)
    {
        try {
            $evento = Evento::find($idEvento);

            if (!$evento) {
                return response()->json(['status' => 'error', 'messages' => ['No se encontró el evento especificado.']], 200);
            }

            $nombreEvento = $evento->descripcion;
            $anioEvento = $evento->created_at->format('Y');

            // Obtener los ganadores
            $ganadores = Ganadores::where('id_evento', $idEvento)->get();

            if ($ganadores->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'evento' => [
                        'nombre' => $nombreEvento,
                        'anio' => $anioEvento,
                    ],
                    'data' => []
                ], 200);
            }


            $ganadoresIds = $ganadores->pluck('id_proyecto')->toArray();


            $equiposParticipantes = explode(',', $evento->equiposParticipantes);
            $equiposRestantes = array_diff($equiposParticipantes, $ganadoresIds);

            // Obtener los proyectos de los participantes restantes
            $resultados = $ganadores->map(function ($ganador) {
                $proyecto = Proyectos::find($ganador->id_proyecto);

                if (!$proyecto) {
                    return null;
                }

                $integrantes = Integrantes::where('proyecto_id', $proyecto->id)->get();
                $lider = $integrantes->firstWhere('id', $proyecto->nombreLider);

                $integrantesOrdenados = $lider
                    ? $integrantes->where('id', '!=', $lider->id)->prepend($lider)
                    : $integrantes;

                return [
                    'posicion' => $ganador->posicion,
                    'proyecto' => [
                        'id' => $proyecto->id,
                        'nombre' => $proyecto->nombreEquipo,
                        'descripcion' => $proyecto->temaProyecto,
                        'fotoEquipo' => $proyecto->foto_equipo,
                        'lider' => $lider ? $lider->nombre_integrante : null,
                        'lider_foto' => $lider ? $lider->foto_integrante : null,
                        'integrantes' => $integrantesOrdenados->map(function ($integrante) {
                            return [
                                'id' => $integrante->id,
                                'nombre' => $integrante->nombre_integrante,
                                'foto_integrante' => $integrante->foto_integrante,
                            ];
                        }),
                    ],
                ];
            })->filter();

            // Obtener los participantes restantes
            $participantes = Proyectos::whereIn('id', $equiposRestantes)
            ->whereIn('estado', [3, 5])
            ->get()
            ->map(function ($proyecto) {
                $integrantes = Integrantes::where('proyecto_id', $proyecto->id)->get();
                $lider = $integrantes->firstWhere('id', $proyecto->nombreLider);

                $integrantesOrdenados = $lider
                    ? $integrantes->where('id', '!=', $lider->id)->prepend($lider)
                    : $integrantes;

                return [
                    'proyecto' => [
                        'id' => $proyecto->id,
                        'nombre' => $proyecto->nombreEquipo,
                        'descripcion' => $proyecto->temaProyecto,
                        'fotoEquipo' => $proyecto->foto_equipo,
                        'lider' => $lider ? $lider->nombre_integrante : null,
                        'lider_foto' => $lider ? $lider->foto_integrante : null,
                        'integrantes' => $integrantesOrdenados->map(function ($integrante) {
                            return [
                                'id' => $integrante->id,
                                'nombre' => $integrante->nombre_integrante,
                                'foto_integrante' => $integrante->foto_integrante,
                            ];
                        }),
                    ],
                ];
            });

            return response()->json([
                'status' => 'success',
                'evento' => [
                    'nombre' => $nombreEvento,
                    'anio' => $anioEvento,
                ],
                'data' => [
                    'ganadores' => $resultados,
                    'participantes' => $participantes,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'warning', 'messages' => [$e->getMessage()]], 200);
        }
    }

    public function detallesEquipo($idProyecto)
    {
        try {
            $proyecto = Proyectos::find($idProyecto);

            if (!$proyecto) {
                return response()->json(['status' => 'error', 'messages' => ['No se encontró el proyecto especificado.']], 200);
            }

            $integrantes = Integrantes::where('proyecto_id', $proyecto->id)->get();
            $lider = $integrantes->firstWhere('id', $proyecto->nombreLider);

            $integrantesOrdenados = $lider
                ? $integrantes->where('id', '!=', $lider->id)->prepend($lider)
                : $integrantes;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'proyecto' => [
                        'id' => $proyecto->id,
                        'nombre' => $proyecto->nombreEquipo,
                        'descripcion' => $proyecto->temaProyecto,
                        'descripcion_equipo' => $proyecto->descripcion_equipo,
                        'eslogan' => $proyecto->eslogan,
                        'fotoEquipo' => $proyecto->foto_equipo,
                        'lider' => $lider ? $lider->nombre_integrante : null,
                        'lider_foto' => $lider ? $lider->foto_integrante : null,
                        'estado' => $proyecto->estado,
                    ],
                    'integrantes' => $integrantesOrdenados->map(function ($integrante) {
                        return [
                            'id' => $integrante->id,
                            'nombre' => $integrante->nombre_integrante,
                            'foto_integrante' => $integrante->foto_integrante,
                        ];
                    }),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' => [$e->getMessage()]], 200);
        }
    }
}
