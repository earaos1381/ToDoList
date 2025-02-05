<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Task;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permisos', []);

        return view('admin.dashboard', [
            'rolesGranted' => $roles,
            'permisosGranted' => $permisos,
        ]);
    }

    public function get()
    {
        try {
            Log::info('DashboardController@getTask');

            $user = Auth::user();

            $tasks = Task::whereRaw("FIND_IN_SET(?, user_assignments)", [$user->id])->get();

            Log::info('DashboardController@getTask - tasks: ' . json_encode($tasks));

            return response()->json(['data' => $tasks], 200);
        } catch (\Exception $e) {
            Log::error('DashboardController@getTask - error: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function createTask(Request $request)
    {
        Log::info('DashboardController@createTask');

        // Validación de los datos de entrada
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'priority_id' => 'required|exists:priorities,id',
            'status_id' => 'required|exists:status,id',
            'task_type_id' => 'required|exists:task_types,id',
            'category_id' => 'required|exists:categories,id',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            // Crear la tarea
            $task = Task::create($request->only([
                'title', 'description', 'due_date', 'priority_id', 'status_id', 'task_type_id', 'category_id'
            ]));

            // Si hay usuarios asignados, los convertimos a JSON y los guardamos
            if ($request->has('assigned_users')) {
                // Convertimos el array de IDs a una cadena con comas
                $task->user_assignments = implode(',', $request->assigned_users);
                $task->save();
            }

            DB::commit();

            // Log de éxito
            Log::info('DashboardController@createTask - task: ' . json_encode($task));

            return response()->json([
                'success' => true,
                'message' => 'Tarea creada con éxito.',
                'task' => $task
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear tarea: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al crear la tarea. Por favor, intente nuevamente.'
            ], 500);
        }
    }

    public function editTask($id)
    {
        Log::info('DependenciasController@editTask');
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Sin datos'], 404);
        }
        Log::info('DependenciasController@editTask - dependencia obtenida');
        return response()->json(['data' => $task]);
    }

    public function updateTask(Request $request, $id)
    {
        Log::info('DashboardController@updateTask - Iniciando actualización');

        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        // Validar los datos recibidos
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'priority_id' => 'required|integer|exists:priorities,id',
            'status_id' => 'required|integer|exists:status,id',
            'task_type_id' => 'required|integer|exists:task_types,id',
            'category_id' => 'required|integer|exists:categories,id',
            'assigned_users' => 'nullable|string'
        ]);

        try {
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'priority_id' => $request->priority_id,
                'status_id' => $request->status_id,
                'task_type_id' => $request->task_type_id,
                'category_id' => $request->category_id,
                'user_assignments' => is_array($request->assigned_users)
                    ? implode(',', $request->assigned_users)
                    : $request->assigned_users
            ]);

            Log::info('DashboardController@updateTask - Tarea actualizada correctamente');

            return response()->json([
                'message' => 'Tarea actualizada exitosamente',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la tarea: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al actualizar la tarea'
            ], 500);
        }
    }

    public function deleteTask($id)
    {
        Log::info('DashboardController@deleteTask - Iniciando eliminación de tarea');

        // Buscar la tarea por ID
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        try {
            // Eliminar la tarea
            $task->delete();

            Log::info('DashboardController@deleteTask - Tarea eliminada correctamente');

            return response()->json([
                'message' => 'Tarea eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la tarea: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al eliminar la tarea'
            ], 500);
        }
    }


}
