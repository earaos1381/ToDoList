<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tablas;
use App\Models\ProyectosAccesos;
use App\Models\Proyectos;
use App\Models\ProyectosTablas;
use Illuminate\Support\Facades\DB;
use Exception;

class AccesosController extends Controller
{
    public function accesos()
    {
        return view('admin.accesos');
    }

    public function obtenerAccesos()
    {
        try {
            $accesos = ProyectosAccesos::orderBy('proyecto_id', 'asc')->get()->map(function ($acceso) {
                $proyecto = Proyectos::find($acceso->proyecto_id);
                $nombreProyecto = $proyecto ? $proyecto->nombreEquipo : 'Sin Nombre';

                $idsTablas = explode(',', $acceso->tabla_recurso);
                $nombresTablas = Tablas::whereIn('id', $idsTablas)->pluck('nombre_tabla')->toArray();

                return [
                    'id' => $acceso->id,
                    'proyecto_id' => $acceso->proyecto_id,
                    'nombre_proyecto' => $nombreProyecto,
                    'id_tablas' => $idsTablas,
                    'nombres_tablas' => $nombresTablas,
                    'created_at' => $acceso->created_at,
                    'updated_at' => $acceso->updated_at,
                ];
            });

            if ($accesos->isEmpty()) {
                return response()->json(['message' => 'No hay Accesos']);
            }

            return response()->json(['acceso' => $accesos]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function obtenerAccesoTablas($id)
    {
        try {
            $acceso = ProyectosAccesos::findOrFail($id);
            $accesoIds = explode(',', $acceso->tabla_recurso);

            $tablas = ProyectosTablas::whereIn('id', $accesoIds)
                ->orderBy('nombre_tabla', 'asc')
                ->get();

            return response()->json($tablas);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los proyectos del evento.'], 500);
        }
    }


    public function agregarAcceso(Request $request)
    {
        try {
            $validated = $request->validate([
                'proyecto_id' => 'required|exists:proyectos,id',
                'tabla_ids' => 'required|array',
                'tabla_ids.*' => 'exists:proyectos_tablas,id'
            ]);

            $tablaIds = implode(',', $validated['tabla_ids']);

            $accesoExistente = ProyectosAccesos::where('proyecto_id', $validated['proyecto_id'])->first();
            if ($accesoExistente) {
                return response()->json(['success' => false,'message' => 'El proyecto ya tiene accesos configurados. Favor de verificar'], 400);
            }

            ProyectosAccesos::create([
                'proyecto_id' => $validated['proyecto_id'],
                'tabla_recurso' => $tablaIds,
            ]);

            return response()->json(['success' => true, 'message' => 'Acceso agregado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al agregar el acceso', 'error' => $e->getMessage()], 500);
        }
    }

    public function detalleAcceso($id)
    {
        try {
            $acceso = ProyectosAccesos::where('id', $id)->first();

            if (!$acceso) {
                return response()->json(['message' => 'El acceso no existe.'], 404);
            }

            $tablaIdsArray = explode(',', $acceso->tabla_recurso);

            return response()->json([
                'proyecto_id' => $acceso->proyecto_id,
                'tabla_ids' => $tablaIdsArray,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los detalles del acceso.'], 500);
        }
    }

    public function editarAcceso(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:proyectos_accesos,id',
                'proyecto_id' => 'required|exists:proyectos,id',
                'tabla_ids' => 'required|array',
                'tabla_ids.*' => 'exists:proyectos_tablas,id'
            ]);

            $tablaIds = implode(',', $validated['tabla_ids']);

            $accesoExistente = ProyectosAccesos::find($validated['id']);
            if (!$accesoExistente) {
                return response()->json(['success' => false, 'message' => 'No se encontró el acceso especificado'], 404);
            }

            $accesoExistente->update([
                'proyecto_id' => $validated['proyecto_id'],
                'tabla_recurso' => $tablaIds,
            ]);

            return response()->json(['success' => true, 'message' => 'Acceso editado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al editar el acceso', 'error' => $e->getMessage()], 500);
        }
    }




    public function eliminarAcceso($id)
    {
        try {

            $acc = ProyectosAccesos::findOrFail($id)->delete();

            return response()->json(['success' => true, 'message' => 'Acceso eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
