<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyectos;
use App\Models\Integrantes;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function index()
    {
        return view('public.login');
    }

    public function registro()
    {
        return view('public.registrar');
    }

    public function validarIntegrantes(Request $request)
    {
        try {
            $request->validate([
                'integrantes' => 'required|array',
                'integrantes.*.nombre_integrante' => 'required|string|max:255',
                'integrantes.*.curp_integrante' => 'required|string|size:18',
                'integrantes.*.telefono_integrante' => 'required|string|max:10',
                'integrantes.*.correo_integrante' => 'required|email|max:255',
            ], [
                'integrantes.*.curp_integrante.size' => 'La CURP debe tener exactamente 18 caracteres.',
                'integrantes.*.correo_integrante.email' => 'El correo electrónico no es válido.',
            ]);

            $integrantes = $request->input('integrantes');

            $curps = array_column($integrantes, 'curp_integrante');
            $correos = array_column($integrantes, 'correo_integrante');

            if (count($curps) !== count(array_unique($curps))) {
                return response()->json(['success' => false, 'message' => 'Hay CURPs duplicadas entre los integrantes.'], 400);
            }

            if (count($correos) !== count(array_unique($correos))) {
                return response()->json(['success' => false, 'message' => 'Hay correos electrónicos duplicados entre los integrantes.'], 400);
            }

            $anioProyecto = date('Y');
            $yaRegistrado = DB::table('integrantes')
                ->whereIn('curp_integrante', $curps)
                ->orWhereIn('correo_integrante', $correos)
                ->where(DB::raw('YEAR(created_at)'), $anioProyecto)
                ->exists();

            if ($yaRegistrado) {
                return response()->json(['success' => false, 'message' => 'Uno o más integrantes ya se encuentran registrados en el mismo año.'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Integrantes válidos para registrar.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function registrarParticipacion(Request $request)
    {
        try {

            $terminos_aceptados = filter_var($request->terminos_aceptados, FILTER_VALIDATE_BOOLEAN);

            $validatedData = $request->validate([
                'nombre_equipo' => 'required|string|max:255',
                'institucion_procedencia' => 'required|string|max:255',
                'carrera_especialidad' => 'required|string|max:255',
                'tema_proyecto' => 'required|string|max:255',
                'nombre_asesor' => 'required|string|max:255',
                'nombre_lider' => 'required|string|max:255',
                'documento_proyecto' => 'required|file|mimes:pdf|max:10000',
                'link_video' => 'required',
                'terminos_aceptados' => 'required',
                'integrantes' => 'required|string'
            ]);

            $integrantes = json_decode($request->integrantes, true);

            $path = $request->file('documento_proyecto')->store('proyectos', 'local');

            $proyecto = Proyectos::create([
                'nombreEquipo' => $validatedData['nombre_equipo'],
                'id_InstPro' => $validatedData['institucion_procedencia'],
                'id_carrera' => $validatedData['carrera_especialidad'],
                'temaProyecto' => $validatedData['tema_proyecto'],
                'nombreAsesor' => $validatedData['nombre_asesor'],
                'documento_proyecto' => $path,
                'linkvideo' => $validatedData['link_video'],
                'terminos' => $terminos_aceptados,
                'estado' => 1,
            ]);

            $liderId = null;

            foreach ($integrantes as $integrante) {

                $nuevoIntegrante = Integrantes::create([
                    'proyecto_id' => $proyecto->id,
                    'nombre_integrante' => $integrante['nombre'],
                    'curp_integrante' => $integrante['curp'],
                    'telefono_integrante' => $integrante['telefono'],
                    'correo_integrante' => $integrante['correo']
                ]);

                if ($integrante['correo'] == $validatedData['nombre_lider']) {
                    $liderId = $nuevoIntegrante->id;
                }
            }

            if ($liderId) {
                $proyecto->nombreLider = $liderId;
                $proyecto->save();
            }

            return response()->json(['success' => true, 'message' => 'Proyecto registrado con éxito']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
