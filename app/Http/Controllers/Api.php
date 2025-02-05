<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Proyectos;
use App\Models\Integrantes;
use App\Models\ProyectosAccesos;
use App\Models\ProyectosTablas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class Api extends Controller
{
    public function NucleoDigital(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return $this->obtenerProyectoDelLider($user->email);
    }

    public function obtenerProyectoDelLider($email)
    {
        $integrante = Integrantes::where('correo_integrante', $email)->first();

        if (!$integrante) {
            return response()->json(['message' => 'No se encontró al integrante.'], 404);
        }

        $proyectoId = $integrante->proyecto_id;
        $proyecto = Proyectos::find($proyectoId);

        if (!$proyecto) {
            return response()->json(['message' => 'No hay proyecto relacionado con este integrante.'], 404);
        }

        $recursosAcceso = ProyectosAccesos::where('proyecto_id', $proyectoId)->pluck('tabla_recurso');
        $recursosIds = $recursosAcceso->flatMap(function($item) {
            return explode(',', $item);
        })->unique()->toArray();

        $recursos = ProyectosTablas::whereIn('id', $recursosIds)->pluck('nombre_tabla');
        $datosDeTablas = $this->obtenerDatosDeTablas($recursos);

        return response()->json([
            'nombreEquipo' => $proyecto->nombreEquipo,
            'datosTablas' => $datosDeTablas,
        ]);
    }

    public function obtenerDatosDeTablas($nombresTablas)
    {
        $datos = [];

        foreach ($nombresTablas as $nombreTabla) {
            switch ($nombreTabla) {
                case 'comedatos_empleados':
                case 'comedatos_institucion':
                    $datos[$nombreTabla] = $this->consultarApiExterna($nombreTabla);
                    break;

                default:
                    $datos[$nombreTabla] = DB::table($nombreTabla)->get();
                    break;
            }
        }

        return $datos;
    }

    public function consultarApiExterna($tabla)
    {
        try {
            $queries = [];

            $apiToken = env('API_TOKEN');

            if ($tabla === 'comedatos_empleados') {
                $queries[] = "SELECT
                                    DIR_Empleados.Nombre,
                                    DIR_Empleados.ApellidoPaterno,
                                    DIR_Empleados.ApellidoMaterno,
                                    CONCAT('https://archivo.transparencia.qroo.gob.mx/SIWQROO/Directorio/Fotos/', DIR_Empleados.Foto, '.JPG') AS Foto,
                                    DIR_Empleados.Email,
                                    DIR_Ubicaciones.Telefono,
                                    DependenciaUbicacion.Denominacion AS Dependencia,
                                    DIR_Ubicaciones.Denominacion AS Unidad_Administrativa
                                FROM
                                    DIR_Empleados
                                LEFT JOIN
                                    DIR_Ubicaciones ON DIR_Empleados.IdUbicacion = DIR_Ubicaciones.IdUbicacion
                                LEFT JOIN
                                    DIR_Nombramientos ON DIR_Empleados.IdNombramiento = DIR_Nombramientos.IdNombramiento
                                LEFT JOIN
                                    DIR_Ubicaciones AS DependenciaUbicacion
                                    ON DependenciaUbicacion.IdUbicacion = SUBSTRING_INDEX(SUBSTRING_INDEX(DIR_Ubicaciones.Ubicacion, '-', 3), '-', -1);";
            } elseif ($tabla === 'comedatos_institucion') {
                $queries[] = "SELECT
                                DIR_Ubicaciones.IdUbicacion,
                                DIR_Ubicaciones.Denominacion AS Nombre_Institucion,
                                DIR_Domicilios.Calle AS Direccion,
                                DIR_Domicilios.Numero,
                                DIR_Domicilios.Colonia,
                                DIR_Domicilios.CodigoPostal,
                                DIR_Domicilios.Referencia,
                                /*DIR_Ubicaciones.Conmutador,
                                DIR_Ubicaciones.Telefono,
                                DIR_Ubicaciones.Extension,
                                DIR_Ubicaciones.Mail,*/
                                DIR_Empleados.Nombre AS Nombre_Encargado,
                                DIR_Empleados.ApellidoPaterno,
                                DIR_Empleados.ApellidoMaterno,
                                DIR_Nombramientos.Nombramiento,
                                DIR_Empleados.Email,
                                DIR_Ubicaciones.Telefono,
                                DIR_Ubicaciones.Extension,
                                CONCAT('https://archivo.transparencia.qroo.gob.mx/SIWQROO/Directorio/Logodep/', DIR_Ubicaciones.IdUbicacion, '.png') AS logo
                                FROM
                                DIR_Ubicaciones
                                LEFT JOIN
                                DIR_Domicilios ON DIR_Ubicaciones.IdDomicilio = DIR_Domicilios.IdDomicilio
                                LEFT JOIN
                                DIR_Empleados ON DIR_Ubicaciones.IdEmpleado = DIR_Empleados.IdEmpleado
                                LEFT JOIN
                                DIR_Nombramientos ON DIR_Empleados.IdNombramiento = DIR_Nombramientos.IdNombramiento
                                WHERE
                                DIR_Ubicaciones.Ubicacion = 'I-1'
                                AND DIR_Ubicaciones.IdUbicacion NOT IN (165, 166)
                                ORDER BY
                                Nombre_Institucion ASC;";
            }

            $results = [];

            foreach ($queries as $query) {
                $response = Http::withOptions(['verify' => false])
                    ->get('http://archivo.transparencia.qroo.gob.mx/SIWQROO/API/api.php', [
                        'token' => $apiToken,
                        'query' => $query,
                    ]);

                if ($response->successful()) {
                    $results[] = $response->json();
                } else {
                    $results[] = ['error' => 'Error al obtener datos de la API externa'];
                }
            }

            return $results;
        } catch (\Exception $e) {
            return ['error' => 'Excepción al consultar la API externa: ' . $e->getMessage()];
        }
    }

}
