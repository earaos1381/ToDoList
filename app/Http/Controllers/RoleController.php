<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    public function roles()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permisos', []);

        return view('admin.roles', [
            'roles' => $roles,
            'permisos' => $permisos,
        ]);
    }

    public function obtenerRoles()
    {
        try {
            $role = Role::all();

            if ($role->isEmpty()) {
                return response()->json(['message' => 'No hay roles']);
            }

            return response()->json(['role' => $role]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function agregarRol(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required',
            ]);

            $existe = Role::where('name', 'LIKE', '%' . $request->input('name') . '%')->first();

            if ($existe) {
                return response()->json(['success' => false, 'message' => 'Ya existe un Rol con un nombre similar.'], 400);
            }

            $rol = new Role();
            $rol->name = $request->input('name');
            $rol->save();

            return response()->json(['success' => true, 'message' => 'Rol agregado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function detalleRol($id)
    {

        $rol = Role::where('id',$id)->get();
        return response()->json($rol);

    }

    public function editarRol(Request $request)
    {
        $validaciones = $request->validate([
            'id' => 'required|integer',
            'name' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'name' => $validaciones['name'],
            ];

            DB::table("roles")->where("id", $validaciones['id'])->update($data);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Rol Editado Exitosamente']);
        } catch (Exception $e) {
            $errors = $e->getMessage();
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $errors]);
        }
    }

    public function eliminarRol($id)
    {
        try {

            $rol = Role::findOrFail($id);

            if ($rol->name === 'Root') {
                return response()->json(['message' => 'No se puede eliminar el rol de Root.'], 400);
            }

            if ($rol->users()->count() > 0) {
                return response()->json(['message' => 'No se puede eliminar el rol porque esta asignado a uno o más usuarios.'], 400);
            }

            $rol->delete();

            return response()->json(['success' => true, 'message' => 'Rol eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function asignarpermisos(Request $request)
    {
        try {
            $idRol = $request->input('idRol');
            $selectedPermisos = $request->input('selectedPermisos');

            $role = Role::find($idRol);

            if (!$role) {
                return response()->json(['error' => 'Rol no encontrado.']);
            }

            $permissions = Permission::find($selectedPermisos);

            $role->syncPermissions($permissions);

            return response()->json(['success' => true, 'message' => 'Permisos asignados correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerPermisosRol($idRol)
    {
        $role = Role::findOrFail($idRol);
        $todosLosPermisos = Permission::all();

        $permisosAsignados = $todosLosPermisos->filter(function ($permiso) use ($role) {
            return $role->hasPermissionTo($permiso);
        });

        return response()->json(['permisos' => $permisosAsignados]);
    }
}
