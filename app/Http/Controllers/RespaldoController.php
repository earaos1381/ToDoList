<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Respaldos;
use ZipArchive;
use File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RespaldoController extends Controller
{

    public function respaldos()
    {
        return view('admin.respaldos');
    }

    public function obtenerRespaldos()
    {
        try {
            $respaldo = Respaldos::all();

            return response()->json(['respaldo' => $respaldo]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmarPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string'
            ]);

            $usuario = Auth::user();

            if (!Hash::check($request->input('password'), $usuario->password)) {
                return response()->json(['success' => false, 'message' => 'La contraseña es incorrecta.'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Contraseña confirmada con éxito.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Se produjo un error al confirmar la contraseña: ' . $e->getMessage()], 500);
        }
    }

    public function realizarRespaldo()
    {

        $resultadoSQL = $this->crearRespaldoSQL();

        if (!$resultadoSQL['success']) {
            Log::error('Error al crear el respaldo SQL: ' . $resultadoSQL['message']);
            return response()->json(['success' => false, 'message' => $resultadoSQL['message']], 500);
        }

        $resultadoAdjunto = $this->crearRespaldoAdjunto();

        if ($resultadoAdjunto['success']) {
            try {
                $respaldo = new Respaldos();
                $respaldo->nombre = $resultadoSQL['nombre'];
                $respaldo->size = $resultadoSQL['size'];
                $respaldo->archivo = $resultadoSQL['ruta'];
                $respaldo->adjunto = $resultadoAdjunto['rutaAdjunto'];
                $respaldo->save();

                return response()->json(['success' => true, 'message' => 'Respaldo realizado correctamente.']);
            } catch (\Exception $e) {
                $rutaSQL = storage_path('app' . str_replace('/storage/', '/', $resultadoSQL['ruta']));
                if (file_exists($rutaSQL)) {
                    if (unlink($rutaSQL)) {
                        Log::info("Archivo SQL eliminado: {$rutaSQL}");
                    } else {
                        Log::error("No se pudo eliminar el archivo SQL: {$rutaSQL}");
                    }
                } else {
                    Log::warning("Archivo SQL no encontrado para eliminar: {$rutaSQL}");
                }

                Log::error('Error al guardar la información del respaldo: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Se produjo un error al guardar la información del respaldo: ' . $e->getMessage()], 500);
            }
        } else {
            $rutaSQL = storage_path('app' . str_replace('/storage/', '/', $resultadoSQL['ruta']));
            if (file_exists($rutaSQL)) {
                if (unlink($rutaSQL)) {
                    Log::info("Archivo SQL eliminado: {$rutaSQL}");
                } else {
                    Log::error("No se pudo eliminar el archivo SQL: {$rutaSQL}");
                }
            } else {
                Log::warning("Archivo SQL no encontrado para eliminar: {$rutaSQL}");
            }

            $mensaje = $resultadoAdjunto['message'] ?? 'Se produjo un error desconocido.';
            Log::error('Error al crear el archivo ZIP: ' . $mensaje);
            return response()->json(['success' => false, 'message' => $mensaje], 500);
        }
    }

    public function crearRespaldoSQL()
    {
        try {
            $fecha = now()->format('Y-m-d_H-i-s');
            $nombreArchivo = "hackaton_{$fecha}.sql";
            $directorio = 'respaldo';

            if (!Storage::exists($directorio)) {
                Storage::makeDirectory($directorio);
            }

            $rutaArchivo = "{$directorio}/{$nombreArchivo}";

            $host = escapeshellarg(env('DB_HOST'));
            $usuario = escapeshellarg(env('DB_USERNAME'));
            $password = escapeshellarg(env('DB_PASSWORD'));
            $baseDeDatos = escapeshellarg(env('DB_DATABASE'));

            $outputFile = storage_path("app/{$rutaArchivo}");

            $comando = "mysqldump --host={$host} --user={$usuario} --password={$password} {$baseDeDatos} > {$outputFile} 2>&1";

            $output = [];
            $returnVar = 0;
            exec($comando, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Error al ejecutar el comando mysqldump. Código de retorno: ' . $returnVar . '. Salida: ' . implode("\n", $output));
            }

            if (Storage::exists($rutaArchivo)) {
                $size = Storage::size($rutaArchivo) / 1024 / 1024;
                return [
                    'success' => true,
                    'nombre' => $nombreArchivo,
                    'ruta' => Storage::url($rutaArchivo),
                    'size' => round($size, 2)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo crear el archivo de respaldo SQL.'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Se produjo un error al realizar el respaldo SQL: ' . $e->getMessage()
            ];
        }
    }

    public function crearRespaldoAdjunto()
    {
        try {
            $fecha = now()->format('Y-m-d_H-i-s');
            $nombreArchivoZIP = "hackaton_adjunto_{$fecha}.zip";
            $directorio = 'respaldo';
            $rutaArchivoZIP = "{$directorio}/{$nombreArchivoZIP}";
            $outputFile = storage_path("app/{$rutaArchivoZIP}");

            if (!Storage::exists($directorio)) {
                Storage::makeDirectory($directorio);
                Log::info("Directorio creado: {$directorio}");
            } else {
                Log::info("Directorio ya existe: {$directorio}");
            }

            $zip = new ZipArchive();

            if ($zip->open($outputFile, ZipArchive::CREATE) === TRUE) {
                $files = Storage::files('proyectos');
                if (empty($files)) {
                    Log::warning('No hay archivos en el directorio "proyectos".');
                }

                foreach ($files as $file) {
                    try {
                        $fileContent = Storage::get($file);
                        $zip->addFromString(basename($file), $fileContent);
                        Log::info("Archivo añadido al ZIP: " . basename($file));
                    } catch (\Exception $e) {
                        Log::error('Error al añadir archivo al ZIP: ' . $e->getMessage());
                    }
                }

                $zip->close();
                Log::info('Archivo ZIP creado correctamente: ' . $outputFile);
            } else {
                Log::error('No se pudo crear el archivo ZIP. Código de error: ' . $zip->statusCode);
                throw new \Exception('No se pudo crear el archivo ZIP.');
            }

            if (Storage::exists($rutaArchivoZIP)) {
                return [
                    'success' => true,
                    'rutaAdjunto' => Storage::url($rutaArchivoZIP)
                ];
            } else {
                Log::error('No se encontró el archivo ZIP después de la creación: ' . $rutaArchivoZIP);
                return [
                    'success' => false,
                    'message' => 'No se pudo crear el archivo de respaldo Zip.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Se produjo un error al realizar el respaldo Zip: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Se produjo un error al realizar el respaldo Zip.'
            ];
        }
    }

    public function descargarRespaldo($id)
    {
        try {
            $respaldo = Respaldos::findOrFail($id);
            $rutaSQL = $respaldo->archivo;
            $rutaZip = $respaldo->adjunto;

            $rutaSQL = storage_path('app' . str_replace('/storage/', '/', $rutaSQL));
            $rutaZip = storage_path('app' . str_replace('/storage/', '/', $rutaZip));

            if (!file_exists($rutaSQL) || !file_exists($rutaZip)) {
                return response()->json(['success' => false, 'message' => 'Uno o ambos archivos no existen.'], 404);
            }

            $nombreArchivoZip = "respaldo_completo_{$id}.zip";
            $rutaArchivoZip = storage_path("app/{$nombreArchivoZip}");

            $zip = new ZipArchive();
            if ($zip->open($rutaArchivoZip, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($rutaSQL, basename($rutaSQL));
                $zip->addFile($rutaZip, basename($rutaZip));
                $zip->close();

                return response()->download($rutaArchivoZip)->deleteFileAfterSend(true);
            } else {
                return response()->json(['success' => false, 'message' => 'No se pudo crear el archivo ZIP.'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Se produjo un error al descargar el respaldo: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarRespaldo($id)
    {
        DB::beginTransaction();
        try {
            $respaldo = Respaldos::findOrFail($id);

            $rutaSQL = storage_path('app' . str_replace('/storage/', '/', $respaldo->archivo));
            $rutaZip = storage_path('app' . str_replace('/storage/', '/', $respaldo->adjunto));


            if (file_exists($rutaSQL)) {
                unlink($rutaSQL);
            }

            if (file_exists($rutaZip)) {
                unlink($rutaZip);
            }

            $respaldo->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Respaldo eliminado exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al eliminar el respaldo: ' . $e->getMessage()], 500);
        }
    }







}
