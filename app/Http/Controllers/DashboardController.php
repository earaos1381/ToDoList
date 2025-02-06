<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\User;
use Exception;
use Carbon\Carbon;

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

            $tasks = Task::where('user_assignments', $user->id)
                        ->with('user')
                        ->get();

            $sharedTasks = DB::table('shared_tasks')
                            ->where('user_id', $user->id)
                            ->pluck('task_id');

            $sharedTaskDetails = Task::whereIn('id', $sharedTasks)
                                    ->with('user')
                                    ->get();

            $allTasks = $tasks->merge($sharedTaskDetails);
            $allTasks = $allTasks->sortByDesc('created_at');

            // Filtrar tareas que están por vencer
            $tasksAboutToExpire = $allTasks->filter(function ($task) {
                $dueDate = Carbon::parse($task->due_date);
                $daysLeft = $dueDate->diffInDays(now());

                return $daysLeft <= 3 && $dueDate->isFuture();
            });

            $tasksAboutToExpire = $tasksAboutToExpire->values()->toArray();

            return response()->json(['data' => $allTasks, 'tasksAboutToExpire' => $tasksAboutToExpire], 200);

        } catch (\Exception $e) {
            Log::error('DashboardController@getTask - error: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function getU()
    {
        try {
            Log::info('UserController@get');
            $users = User::all();

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay usuarios']);
            }

            $usersRoles = $users->map(function($user) {

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });
            return response()->json(['data' => $usersRoles]);
        } catch (\Exception $e) {
            Log::error('UserController@get - error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getAssigned($taskId)
    {
        try {
            Log::info('DashboardController@getAssignedUsers');

            // Obtener los usuarios asignados a la tarea
            $assignedUsers = DB::table('shared_tasks')
                ->where('task_id', $taskId)
                ->pluck('user_id');  // Devuelve una colección de los IDs de usuarios

            return response()->json($assignedUsers, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener los usuarios asignados: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los usuarios asignados'], 500);
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
            // Crear la tarea con el usuario que la registra
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'priority_id' => $request->priority_id,
                'status_id' => $request->status_id,
                'task_type_id' => $request->task_type_id,
                'category_id' => $request->category_id,
                'user_assignments' => Auth::id(),
            ]);

            DB::commit();

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

    public function shareTask(Request $request)
    {
        try {
            Log::info('DashboardController@shareTask');

            $rules = [
                'task_id' => 'required|exists:tasks,id',
            ];

            if (!empty($request->user_ids)) {
                $rules['user_ids'] = 'required|array';
                $rules['user_ids.*'] = 'exists:users,id';
            }

            $request->validate($rules);

            $task = Task::findOrFail($request->task_id);

            if (empty($request->user_ids)) {
                DB::table('shared_tasks')->where('task_id', $task->id)->delete();
            } else {
                $currentAssignedUsers = DB::table('shared_tasks')
                    ->where('task_id', $task->id)
                    ->pluck('user_id')
                    ->toArray();

                $usersToRemove = array_diff($currentAssignedUsers, $request->user_ids);

                DB::table('shared_tasks')
                    ->where('task_id', $task->id)
                    ->whereIn('user_id', $usersToRemove)
                    ->delete();

                $usersToAdd = array_diff($request->user_ids, $currentAssignedUsers);

                foreach ($usersToAdd as $userId) {
                    DB::table('shared_tasks')->insert([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                    ]);
                }
            }

            return response()->json(['message' => 'Tarea compartida exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error al compartir tarea: ' . $e->getMessage());
            return response()->json(['message' => 'Error al compartir la tarea'], 500);
        }
    }







}
