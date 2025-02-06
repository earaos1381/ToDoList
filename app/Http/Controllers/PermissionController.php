<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;

class PermissionController extends Controller
{
    public function permisos()
    {
        $roles = session('user_role', []);
        $permisos = session('user_permisos', []);

        return view('admin.permisos', [
            'rolesGranted' => $roles,
            'permisosGranted' => $permisos,
        ]);
    }

    public function obtenerPermisos()
    {
        try {
            $permiso = Permission::all();

            if ($permiso->isEmpty()) {
                return response()->json(['message' => 'No hay permisos']);
            }

            return response()->json(['permiso' => $permiso]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function agregarPermisos(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);

            $existe = Permission::where('name', 'LIKE', '%' . $request->input('name') . '%')->first();

            if ($existe) {
                return response()->json(['success' => false, 'message' => 'Ya existe un Permiso con un nombre similar.'], 400);
            }

            $permiss = new Permission();
            $permiss->name = $request->input('name');
            $permiss->save();

            return response()->json(['success' => true, 'message' => 'Permiso agregado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function detallePermiso($id)
    {

        $permiso = Permission::where('id',$id)->get();
        return response()->json($permiso);

    }

    public function editarPermiso(Request $request)
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

            DB::table("permissions")->where("id", $validaciones['id'])->update($data);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permiso Editado Exitosamente']);
        } catch (Exception $e) {
            $errors = $e->getMessage();
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $errors]);
        }
    }

    public function eliminarPermiso($id)
    {
        try {

            $permission = Permission::findOrFail($id);

            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el permiso porque está asociado con uno o más roles.'
                ], 400);
            }

            $permission->delete();

            return response()->json(['success' => true, 'message' => 'Permiso eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
