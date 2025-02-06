<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

use Exception;

class UserController extends Controller
{

    public function index()
    {
        return view('public.login');
    }

    public function login(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return response()->json(['status' => 'success', 'redirect' => route('dashboard')]);
            } else {
                return response()->json(['status' => 'error', 'messages' => ['Usuario o contraseña incorrecto.']], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => [$e->getMessage()]], 500);
        }
    }

    public function register()
    {
        return view('public.signup');
    }

    public function signup(Request $request)
    {

        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'email.unique' => 'El correo electrónico ya está registrado.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);

            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->save();

            $user->assignRole('Usuario');

            auth()->login($user);

            return response()->json(['status' => 'success', 'redirect' => route('dashboard')]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'messages' => [$e->getMessage()]], 500);
        }

    }

    public function usuarios()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permisos', []);

        return view('admin.usuarios', [
            'rolesGranted' => $roles,
            'permisosGranted' => $permisos,
        ]);
    }

    public function get()
    {
        try {
            Log::info('UserController@get');
            $users = User::all();

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay usuarios']);
            }

            $usersRoles = $users->map(function($user) {
                $roles = $user->roles()->pluck('name');

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles->toArray(),
                ];
            });
            Log::info('UserController@get - users: ' . json_encode($usersRoles));
            return response()->json(['data' => $usersRoles]);
        } catch (\Exception $e) {
            Log::error('UserController@get - error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function agregarUsuario(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            $existeUser = User::where('email', $request->input('email'))->first();

            if ($existeUser) {
                return response()->json(['success' => false, 'message' => 'Ya existe un Usuario con ese correo.'], 400);
            }

            $nuevoUsuario = new User();
            $nuevoUsuario->name = $request->input('name');
            $nuevoUsuario->email = $request->input('email');
            $nuevoUsuario->password = Hash::make($request->input('password'));
            $nuevoUsuario->save();
            $nuevoUsuario->assignRole('Usuario');

            return response()->json(['success' => true, 'message' => 'Usuario agregado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function detalleUsuario($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function editarUsuario(Request $request)
    {

        $validaciones = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'nullable|string|min:8',
        ], [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($validaciones['id']);

            if (!empty($validaciones['password'])) {
                $user->password = Hash::make($validaciones['password']);
            }

            $user->name = $validaciones['name'];
            $user->email = $validaciones['email'];
            $user->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Usuario editado exitosamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminarUsuario($id)
    {
        try {

            $user = User::findOrFail($id);
            $usuarioAutenticado = auth()->user();

            if ($user->id == $usuarioAutenticado->id) {
                return response()->json(['message' => 'No puedes eliminar tu propio Usuario.'], 400);
            }

            if ($user->hasRole('Root')) {
                $rootCount = User::role('Root')->count();

                if ($rootCount <= 1) {
                    return response()->json(['message' => 'No se puede eliminar el único Root'], 400);
                }
            }

            $user->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el Usuario'], 500);
        }
    }

    public function asignarRoles(Request $request)
    {
        try {
            $idUser = $request->input('idUser');
            $selectedRoles = $request->input('selectedRoles');
            $authUser = auth()->user();
            $user = User::find($idUser);

            if (!$user) {
                return response()->json(['message' => 'Rol no encontrado.']);
            }

            if ($authUser->id === $user->id) {
                return response()->json(['message' => 'No puedes cambiar tu propio rol.'], 403);
            }

                if ($authUser->hasRole('Root')) {
                $rootCount = User::role('Root')->count();
                if ($rootCount === 1 && $user->hasRole('Root') && !in_array('Root', $selectedRoles)) {
                    return response()->json(['message' => 'No puedes eliminar el rol de root del único root en el sistema.'], 403);
                }
            }

            $roles = Role::find($selectedRoles);

            $user->syncRoles($roles);

            return response()->json(['success' => true, 'message' => 'Rol asignado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function obtenerRolesUsuario($idUser)
    {
        $user = User::findOrFail($idUser);
        $todosLosRoles = Role::all();
        $rolesAsignados = $todosLosRoles->filter(function ($role) use ($user) {
            return $user->hasAnyRole($role);
        });

        return response()->json(['roles' => $rolesAsignados]);
    }


    public function password()
    {
        $layout = 'layouts.master';
        $user = Auth::user();

        if($user->hasRole('Juez')){
            $layout = 'layouts.juezmaster';
        }

        return view('admin.password', compact('layout'));
    }

    public function actualizarPassword(Request $request)
    {

        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&.]/'
            ],
            'confirmPassword' => 'required|string|same:newPassword',
        ], [
            'newPassword.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'newPassword.regex' => 'La nueva contraseña debe contener al menos una letra minúscula, una letra mayúscula, un número y un símbolo especial (@$!%*#?&).',
            'confirmPassword.same' => 'La confirmación de contraseña no coincide con la nueva contraseña.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->currentPassword, $user->password)) {
            return response()->json(['success' => false, 'message' => 'La contraseña actual es incorrecta.']);
        }

        if (Hash::check($request->newPassword, $user->password)) {
            return response()->json(['success' => false, 'message' => 'La nueva contraseña no puede ser la misma que la actual.']);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Contraseña actualizada con éxito. Inicie Sesión de nuevo.']);
    }

    public function actualizarInfoUser(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($user->isDirty()) {
            $user->save();
            return response()->json(['success' => true, 'message' => 'Perfil actualizado con éxito.']);
        }

        return response()->json(['success' => false, 'message' => 'No se realizaron cambios en el perfil.']);
    }

    public function obtenerInfoUser()
    {
        $user = Auth::user();
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');

    }
}
