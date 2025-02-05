<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tablas;
use App\Models\ProyectosAccesos;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Exception;

class TablasController extends Controller
{
    public function tablas()
    {
        return view('admin.tablas');
    }

    public function obtenerTablas()
    {
        try {
            $tablas = Tablas::orderBy('id', 'asc')->get();

            if ($tablas->isEmpty()) {
                return response()->json(['message' => 'No hay Tablas']);
            }

            return response()->json(['tabla' => $tablas]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /* public function crearTabla(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'campos' => 'required|array',
            'campos.*.nombre' => 'required|string',
            'campos.*.tipo' => 'required|string',
        ]);

        try {
            $nombreTabla = $validated['nombre'];
            $campos = $validated['campos'];

            $timestamp = date('Y_m_d_His');
            $migrationFileName = database_path('migrations/' . $timestamp . '_create_' . $nombreTabla . '_table.php');
            $migrationClassName = 'Create' . ucfirst($nombreTabla) . 'Table';

            if (file_exists($migrationFileName)) {
                return response()->json(['success' => false, 'message' => 'La migración ya existe.']);
            }

            $content = '<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class ' . $migrationClassName . ' extends Migration
    {
        public function up()
        {
            Schema::create("' . $nombreTabla . '", function (Blueprint $table) {';

            foreach ($campos as $campo) {
                $content .= '
                $table->' . strtolower($campo['tipo']) . '("' . $campo['nombre'] . '");';
            }

            $content .= '
            });
        }

        public function down()
        {
            Schema::dropIfExists("' . $nombreTabla . '");
        }
    }';

            file_put_contents($migrationFileName, $content);

            if (!file_exists($migrationFileName)) {
                return response()->json(['success' => false, 'message' => 'Error al guardar el archivo de migración.']);
            }

            Artisan::call('migrate', ['--path' => 'database/migrations/' . $timestamp . '_create_' . $nombreTabla . '_table.php']);

            return response()->json(['success' => true, 'message' => 'Migración y tabla creadas correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear la tabla: ' . $e->getMessage()]);
        }
    } */





    public function agregarTabla(Request $request)
    {
        try {
            $request->validate([
                'descripcion' => 'required',
            ]);

            $existeTabla = Tablas::where('nombre_tabla', 'LIKE', '%' . $request->input('descripcion') . '%')->first();

            if ($existeTabla) {
                return response()->json(['success' => false, 'message' => 'Ya existe una Tabla con un nombre similar.'], 400);
            }

            $tabla = new Tablas();
            $tabla->nombre_tabla = $request->input('descripcion');
            $tabla->save();

            return response()->json(['success' => true, 'message' => 'Tabla agregada exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function detalleTabla($id)
    {

        $tabla = Tablas::where('id',$id)->get();
        return response()->json($tabla);

    }

    public function editarTabla(Request $request)
    {
        $validaciones = $request->validate([
            'id' => 'required|integer',
            'descripcion' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'nombre_tabla' => $validaciones['descripcion'],
            ];

            DB::table("proyectos_tablas")->where("id", $validaciones['id'])->update($data);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Tabla Editada Exitosamente']);
        } catch (Exception $e) {
            $error = $e->getMessage();
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $error]);
        }
    }

    public function eliminarTabla($id)
    {
        try {

            $uni = Tablas::findOrFail($id);
            $tablausandoproyecto = ProyectosAccesos::whereRaw("FIND_IN_SET(?, tabla_recurso)", [$id])->exists();

            if ($tablausandoproyecto) {
                return response()->json(['message' => 'No se puede eliminar esta Tabla porque está asociada a uno o más proyectos.'], 400);
            }

            $uni->delete();

            return response()->json(['success' => true, 'message' => 'Tabla eliminada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
