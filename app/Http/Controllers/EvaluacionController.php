<?php

namespace App\Http\Controllers;

use App\Models\Empates;
use App\Models\Ganadores;
use App\Models\ResultadosFinales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Proyectos;
use App\Models\Preguntas;
use App\Models\Respuestas;
use App\Models\Calificacion;
use App\Models\Integrantes;
use App\Models\Observaciones;
use App\Models\Evento;

use Exception;

class EvaluacionController extends Controller
{

    public function index($idProyecto)
    {
        return view('admin.evaluacion', compact('idProyecto'));
    }

    public function encriptar(Request $request){

        $encriptado = Crypt::encryptString($request->id);
        $urlQR = url('/login?proyectoId=' . $encriptado);

        $response = [
            'url' => $urlQR
        ];

        return response()->json($response, 200);
    }

    public function evaluacionLogin(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'idProyecto' => 'required'
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if($user->hasRole('Juez')){
                    $request->session()->regenerate();
                    return response()->json(['status' => 'success', 'redirect' => route('evaluacion', ['idProyecto' => $request->idProyecto])]);
                } else {
                    return response()->json(['status' => 'error', 'messages' => ['El Usuario proporcionado no es un Juez.']], 400);
                }

            } else {
                return response()->json(['status' => 'error', 'messages' => ['Usuario o contraseña incorrecto.']], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => [$e->getMessage()]], 500);
        }
    }

    public function obtenerInfoProyecto($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $proyecto = Proyectos::find($id);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado.'], 404);
        }

        /* obtener el evento al que pertenece el proyecto */
        $eventos = Evento::orderBy('fechaInicio', 'desc')->get();
        $nombreEvento = '';

        foreach($eventos as $event){
            $equiposConcatenados = $event->equiposParticipantes;
            $arrayEquipos = explode(',', $equiposConcatenados);
            $arrayEquipos = array_map('intval', $arrayEquipos);

            if (in_array($proyecto->id, $arrayEquipos)) {
                $nombreEvento = $event->descripcion . " " . date('Y', strtotime($event->fechaInicio));
                break;
            }
        }

        $response = [
            'proyecto' => $proyecto,
            'evento' => $nombreEvento,
        ];

        return response()->json($response, 200);
    }

    public function obtenerPreguntasPublicadas($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $proyecto = Proyectos::find($id);

        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado.'], 404);
        }

        $user = Auth::user();
        $idUserEncriptado = Crypt::encryptString($user->id);

        /* FORMULARIO INACTIVO*/
        $eventos = Evento::orderBy('fechaInicio', 'desc')->get();
        $idEvento = 0;
        $casoEmpate = 0;

        foreach($eventos as $event){
            $equiposConcatenados = $event->equiposParticipantes;
            $arrayEquipos = explode(',', $equiposConcatenados);
            $arrayEquipos = array_map('intval', $arrayEquipos);

            if (in_array($proyecto->id, $arrayEquipos)) {
                if($event->preguntas_publicadas == 0){
                    return response()->json(['message' => 'Formulario Inactivo. Espere la indicación de los organizadores para poder acceder.'], 404);
                }

                $idEvento = $event->id;
                $casoEmpate = $event->caso_empate;
                break;
            }
        }

        $idEventoEncriptado = Crypt::encryptString($idEvento);

        if ($idEvento === 0) {
            return response()->json([
                'message' => 'El proyecto no pertenece a ningún evento.',
                'redirect' => route('evaluacionDashboard', [
                    'idJuez' => $idUserEncriptado,
                    'idEvento' => $idEventoEncriptado,
                    ])
                ], 404);
        }

        /* REVISAR SI EL JUEZ YA CALIFICO ESTE PROYECTO */
        $existeCalificacion  = Calificacion::where('id_juez', $user->id)
                                    ->where('id_proyecto', $proyecto->id)->get();

        if(count($existeCalificacion) != 0 && $casoEmpate == 0){
            return response()->json([
                'message' => 'Usted ya ha calificado este proyecto',
                'redirect' => route('evaluacionDashboard', [
                    'idJuez' => $idUserEncriptado,
                    'idEvento' => $idEventoEncriptado
                ])
            ], 404);
        }

        else if (count($existeCalificacion) != 0 && $casoEmpate == 1){
            return response()->json(['message' => 'Caso de Empate', 'redirect' => route('empates', ['idEvento' => $idEventoEncriptado])], 404);
        }

        /* TRAE LAS PREGUNTAS PUBLICADAS */
        $preguntas = Preguntas::where('id_evento', $idEvento)
                    ->where('estado',1)->get();

        if (count($preguntas) < 1) {
            return response()->json(['message' => 'Este evento no tiene preguntas activas.'], 404);
        }

        /* parte de preguntas dinamicas con respuestas dinamicas */
        /*foreach ($preguntas as $pregunta) {
            $respuestas = Respuestas::where('id_pregunta', $pregunta->id)->get(); // Asumo que la relación es con Respuestas

            $pregunta->respuestas = $respuestas;
        }*/

        return response()->json($preguntas, 200);
    }

    public function guardarCalificaciones(Request $request)
    {
        try {
            $resultadoFinalSuma = 0;
            $id = Crypt::decryptString($request->proyecto);

            $user = Auth::user();
            $idUser = $user->id;

            $validarExistencia = Calificacion::where('id_juez', $idUser)->where('id_proyecto',$id)->get();

            if($validarExistencia->count() == 0){
                // Decodificar el JSON en un array
                $respuestas = json_decode($request->respuestas, true);

                foreach($respuestas as $respuesta){
                    $nuevaRespuesta = new Calificacion();

                    $nuevaRespuesta->id_pregunta = $respuesta['idPregunta'];
                    $nuevaRespuesta->posicion_respuesta = $respuesta['respuestaValor'];
                    $nuevaRespuesta->id_juez = $idUser;
                    $nuevaRespuesta->id_proyecto = $id;

                    /* Calculo para obtener el valor de la pregunta */
                    $pregunta = Preguntas::find($respuesta['idPregunta']);
                    $valorPregunta = $pregunta->valor;

                    $resultadoRegladeTres = ($valorPregunta * $respuesta['respuestaValor']) / 10; // entre 10 pq es el max valor de entre las respuestas
                    $nuevaRespuesta->calificacion_pregunta = $resultadoRegladeTres;

                    $nuevaRespuesta->save();

                    /* Calculo para resultados finales */
                    $resultadoFinalSuma += $resultadoRegladeTres;
                }

                $eventos = Evento::orderBy('fechaInicio', 'desc')->get();
                $idEvento = 0;

                foreach($eventos as $event){
                    $equiposConcatenados = $event->equiposParticipantes;

                    $arrayEquipos = explode(',', $equiposConcatenados);
                    $arrayEquipos = array_map('intval', $arrayEquipos);

                    if (in_array($id, $arrayEquipos)) {
                        $idEvento = $event->id;
                        break;
                    }
                }

                $nuevoResultadoFinal = new ResultadosFinales();
                $nuevoResultadoFinal->id_evento = $idEvento;
                $nuevoResultadoFinal->id_juez = $idUser;
                $nuevoResultadoFinal->id_proyecto = $id;
                $nuevoResultadoFinal->calificacion_final = $resultadoFinalSuma;
                $nuevoResultadoFinal->save();

                $idJuez = Crypt::encryptString($idUser);
                $idEvento = Crypt::encryptString($idEvento);

                return response()->json([
                    'status' => 'success',
                    'redirect' => route('evaluacionDashboard', ['idEvento' => $idEvento, 'idJuez' => $idJuez]),
                ]);
            }
            else {
                return response()->json(['status' => 'error', 'messages' => 'Ya se tiene su registro a este proyecto'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => [$e->getMessage()]], 500);
        }
    }

    public function evaluacionDashboard(Request $request)
    {
        session([
            'idJuez' => $request->idJuez,
            'idEvento' => $request->idEvento,
        ]);

        return view('admin.evaluacionDashboard');
    }

    public function obtenerProyectosRevisados(Request $request)
    {
        $idJuez = Crypt::decryptString($request->idJuez);
        $idEvento = Crypt::decryptString($request->idEvento);

        $evento = Evento::find($idEvento);
        $equiposParticipantesConcatenados = $evento->equiposParticipantes;

        $arrayEquipos = array_map('intval', explode(',', $equiposParticipantesConcatenados));

        // Obtener todas las calificaciones del juez
        $juezCalificaciones = Calificacion::whereIn('id_proyecto', $arrayEquipos)
                                ->where('id_juez', $idJuez)
                                ->orderBy('created_at', 'desc')->get();

        // Si no hay calificaciones, retorna un mensaje de error
        if ($juezCalificaciones->isEmpty()) {
            return response()->json(['message' => 'El juez no ha dado calificaciones.'], 404);
        }

        $proyectos = [];
        foreach ($juezCalificaciones as $el) {
            $proyecto = Proyectos::find($el->id_proyecto);

            if ($proyecto && !in_array($proyecto, $proyectos)) {
                $proyectos[] = $proyecto;
            }
        }

        return response()->json($proyectos, 200);
    }

    public function obtenerProyectosActivos($idEvento)
    {
        try {
            /* solo traer los proyectos aceptados y que sean del evento*/
            $idEvento = Crypt::decryptString($idEvento);

            $evento = Evento::find($idEvento);
            $equiposParticipantesConcatenados = $evento->equiposParticipantes;

            $arrayEquipos = array_map('intval', explode(',', $equiposParticipantesConcatenados));

            $proyectos = Proyectos::whereIn('id', $arrayEquipos)
                       ->where('estado', '3')
                       ->orderBy('nombreEquipo', 'asc')
                       ->get();

            $lideresIds = $proyectos->pluck('nombreLider')->toArray();
            $lideres = Integrantes::whereIn('id', $lideresIds)->get()->keyBy('id');

            foreach ($proyectos as $proyecto) {

                if (isset($lideres[$proyecto->nombreLider])) {
                    $proyecto->nombreLiderNombre = $lideres[$proyecto->nombreLider]->nombre_integrante;
                } else {
                    $proyecto->nombreLiderNombre = 'N/A';
                }

            }

            return response()->json($proyectos);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function obtenerEventoNombreFecha($idEvento)
    {
        try {

            $id = Crypt::decryptString($idEvento);
            $evento = Evento::find($id);


            $nombreEvento = $evento->descripcion;
            $fechaEvento = date('Y', strtotime($evento->fechaInicio));

            return response()->json(['nombreEvento' => $nombreEvento, 'fechaEvento' => $fechaEvento], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function empates($idEvento)
    {
        return view('admin.empates', compact('idEvento'));
    }



    public function obtenerProyectosConEmpate($idEvento)
    {
        try {

            $idEventoEncriptado = Crypt::decryptString($idEvento);
            $idEvento = (int)$idEventoEncriptado;

            $evento = Evento::find($idEvento);

            $idJuezAutenticado = auth()->user()->id;

            // Verificar si el juez autenticado es parte de los jueces del evento
            $juecesParticipantes = explode(',', ltrim($evento->juecesParticipantes, ',')); // Eliminar la primera coma y separar por comas
            if (!in_array($idJuezAutenticado, $juecesParticipantes)) {
                return response()->json(['message' => 'El usuario no es juez en este evento.'], 403);
            }

            // Verificar si el juez ya ha emitido su voto para este evento
            $votoExistente = Empates::where('id_evento', $idEvento)
                                ->where('id_juez', $idJuezAutenticado)
                                ->exists();

            if ($votoExistente) {
                // Si ya ha votado, retornar un mensaje de alerta
                return response()->json(['message' => 'El juez ya ha emitido su voto para este evento.'], 403);
            }


            $nombreEvento = $evento->descripcion;
            $fechaEvento = date('Y', strtotime($evento->fechaInicio));

            //Traer las calificaciones de los proyectos
            $equiposParticipantesConcatenados = $evento->equiposParticipantes;
            $arrayEquipos = array_map('intval', explode(',', $equiposParticipantesConcatenados));

            $proyectosAgrupados = Proyectos::whereIn('id', $arrayEquipos)
                                    ->selectRaw('id, nombreEquipo, temaProyecto, calificacion_final_promedio as total_calificacion, foto_equipo')
                                    ->orderBy('total_calificacion', 'desc')
                                    ->get();

            // Determinar las calificaciones del top 3
            $calificacionesTop3 = $proyectosAgrupados
                ->pluck('total_calificacion')
                ->take(3); // Solo tomar las 3 mejores calificaciones

            // Filtrar los proyectos que tienen calificaciones dentro del top 3
            $proyectosTop3 = $proyectosAgrupados->filter(function ($proyecto) use ($calificacionesTop3) {
                return $calificacionesTop3->contains($proyecto->total_calificacion);
            });

            // Determinar los resultados y detectar empates
            $resultadosFinales = [];
            $ranking = 1;
            $empatesProcesados = [];

            foreach ($proyectosTop3 as $proyecto) {
                // Verificar si esta calificación ya fue procesada como empate
                if (in_array($proyecto->total_calificacion, $empatesProcesados)) {
                    continue;
                }

                // Verificar si hay otros registros con la misma calificación
                $registrosEmpatados = $proyectosTop3->where('total_calificacion', $proyecto->total_calificacion);

                if ($registrosEmpatados->count() > 1) {
                    // Es un empate
                    $resultadosFinales['empates'][$ranking] = [
                        'calificacion' => $proyecto->total_calificacion,
                        'proyectos' => $registrosEmpatados->values()->toArray(),
                        'lugar' => $ranking,
                    ];

                    $empatesProcesados[] = $proyecto->total_calificacion;

                    $ranking += $registrosEmpatados->count();
                } else {
                    // No es empate
                    $resultadosFinales['puestos'][$ranking] = [
                        'proyecto' => $proyecto->id,
                        'calificacion' => $proyecto->total_calificacion,
                        'lugar' => $ranking,
                    ];

                    $ranking++;
                }
            }

            return response()->json(['nombreEvento' => $nombreEvento, 'fechaEvento' => $fechaEvento, 'resultados' => $resultadosFinales], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function registrarVotos(Request $request)
    {
        try {

            $request->validate([
                'idEvento' => 'required',
                'idProyecto' => 'required',
                'votos' => 'required',
            ]);

            $user = Auth::user();
            $idEvento = Crypt::decryptString($request->input('idEvento'));

            $empates = new Empates();
            $empates->id_evento = $idEvento;
            $empates->id_juez = $user->id;
            $empates->id_proyecto = $request->input('idProyecto');
            $empates->votos = $request->input('votos');
            $empates->save();

            /* validar si ya todos los jueces votaron */
            $darResultados = $this->validarTodosJuecesVotaron($idEvento);

            /* hacer el calculo y publicar ganadores */
            if($darResultados) {
                $this->contabilizarVotosDesempate($idEvento);

                $e = Evento::find($idEvento);

                $e->ganadores_publicados = 1;
                $e->updated_at = now();
                $e->save();
            }

            return response()->json(['success' => true, 'message' => 'Voto registrado', 'darResultados' => $darResultados]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function validarTodosJuecesVotaron($idEvento): bool
    {

        $evento = Evento::find($idEvento);

        $juecesParticipantes = array_map('trim', explode(',', $evento->juecesParticipantes));
        $arrayJueces = array_filter($juecesParticipantes, 'is_numeric');
        $cantidadJueces = count($arrayJueces);

        // Traer los votos de los jueces para este evento
        $empates = Empates::whereIn('id_juez', $arrayJueces)
                        ->where('id_evento', $idEvento)
                        ->get();

        $juecesUnicos = [];
        foreach ($empates as $empate) {
            if (!in_array($empate->id_juez, $juecesUnicos)) {
                $juecesUnicos[] = $empate->id_juez;
            }
        }

        $juecesConVotos = count($juecesUnicos);
        if ($juecesConVotos < $cantidadJueces) { // si falta alguien en votar
            return false;
        }

        // Agrupar los votos por juez
        $votosPorJuez = $empates->groupBy('id_juez')->map->count();

        // Verificar si todos los jueces tienen la misma cantidad de votos
        $conteos = $votosPorJuez->values()->unique();

        // Si hay más de un valor único, significa que no todos tienen el mismo número de votos
        if ($conteos->count() > 1) {
            return false;
        }

        // Si todos los jueces tienen el mismo número de votos
        return true;
    }

    public function contabilizarVotosDesempate($idEvento)
    {
        try {
            $empates = Empates::where('id_evento', $idEvento)->get();

            // Agrupar los votos por proyecto
            $votosPorProyecto = $empates->groupBy('id_proyecto')->map(function ($items, $idProyecto) {
                return [
                    'id_proyecto' => $idProyecto,
                    'cantidad_votos' => $items->count(),
                    'total_votos' => $items->sum('votos'),
                ];
            });

            $idEvento = (int)$idEvento;
            $evento = Evento::find($idEvento);

            if (!$evento) {
                return response()->json(['success' => false, 'message' => 'Evento no encontrado'], 404);
            }

            $equiposParticipantesConcatenados = $evento->equiposParticipantes;
            $arrayEquipos = array_map('intval', explode(',', $equiposParticipantesConcatenados));

            $proyectosAgrupados = Proyectos::whereIn('id', $arrayEquipos)
                ->selectRaw('id, nombreEquipo, temaProyecto, calificacion_final_promedio as total_calificacion')
                ->orderBy('total_calificacion', 'desc')
                ->get();

            $lugaresMaximos = 3;
            $ranking = 1;
            $ganadoresInsertados = 0;
            $proyectosProcesados = [];

            foreach ($proyectosAgrupados as $proyecto) {
                if ($ganadoresInsertados >= $lugaresMaximos) {
                    break;
                }

                if (in_array($proyecto->id, $proyectosProcesados)) {
                    continue;
                }

                $proyectosEmpatados = $proyectosAgrupados->where('total_calificacion', $proyecto->total_calificacion);

                if ($proyectosEmpatados->count() > 1) {
                    // Resolver empate por votos
                    $proyectosOrdenadosPorVotos = $proyectosEmpatados->map(function ($proyectoEmpatado) use ($votosPorProyecto) {
                        $idProyecto = $proyectoEmpatado->id;
                        $totalVotos = $votosPorProyecto->get($idProyecto)['total_votos'] ?? 0;
                        return [
                            'id_proyecto' => $idProyecto,
                            'nombreEquipo' => $proyectoEmpatado->nombreEquipo,
                            'temaProyecto' => $proyectoEmpatado->temaProyecto,
                            'calificacion' => $proyectoEmpatado->total_calificacion,
                            'total_votos' => $totalVotos,
                        ];
                    })->sortByDesc('total_votos')->values();

                    foreach ($proyectosOrdenadosPorVotos as $proyectoOrdenado) {
                        if ($ganadoresInsertados >= $lugaresMaximos) {
                            break;
                        }

                        $ganador = new Ganadores();
                        $ganador->id_evento = $idEvento;
                        $ganador->id_proyecto = $proyectoOrdenado['id_proyecto'];
                        $ganador->posicion = $ranking;
                        $ganador->save();

                        $proyectosProcesados[] = $proyectoOrdenado['id_proyecto'];
                        $ranking++;
                        $ganadoresInsertados++;
                    }
                } else {
                    // Insertar directamente si no hay empate
                    $ganador = new Ganadores();
                    $ganador->id_evento = $idEvento;
                    $ganador->id_proyecto = $proyecto->id;
                    $ganador->posicion = $ranking;
                    $ganador->save();

                    $proyectosProcesados[] = $proyecto->id;
                    $ranking++;
                    $ganadoresInsertados++;
                }
            }

            return response()->json(['success' => true, 'message' => 'Clasificación procesada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


}
