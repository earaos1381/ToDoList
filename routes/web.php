<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RespaldoController;
use App\Http\Controllers\EvaluacionController;

use App\Http\Controllers\TablasController;
use App\Http\Controllers\AccesosController;

use App\Http\Controllers\CalificacionesController;
use App\Http\Controllers\GanadoresController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/*--------------------------------------------------------------------------
| Index                                                                    |
|--------------------------------------------------------------------------*/
Route::get('/', [PublicController::class, 'index']); //
Route::get('/Obtenerimagenes/{tipo?}', [ImagesController::class, 'Obtenerimagenes']);
Route::get('/registro', [PublicController::class, 'registro']);

Route::get('/verificarRol', [UserController::class, 'verificarRol']);


Route::get('/ganadores', [GanadoresController::class, 'ganadoresEvento'])->name('ganadoresEvento');
Route::get('/ganadores/{idEvento}', [GanadoresController::class, 'obtenerGanadores']);
Route::get('/detallesEquipo/{idEquipo}', [GanadoresController::class, 'detallesEquipo']);




/*--------------------------------------------------------------------------
| Log in                                                                    |
|--------------------------------------------------------------------------*/
Route::get('/login', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::post('/encriptar', [EvaluacionController::class, 'encriptar']);
Route::post('/login/evaluacion', [EvaluacionController::class, 'evaluacionLogin']);




/*--------------------------------------------------------------------------
| Sing up                                                                    |
|--------------------------------------------------------------------------*/
Route::get('/register', [UserController::class, 'register']);
Route::post('/register', [UserController::class, 'signup']);


/*--------------------------------------------------------------------------
| Dashboard                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Dashboard'])->prefix('Dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard'); /*  */
    Route::get('/get', [DashboardController::class, 'get']); /*  */
    Route::post('/create', [DashboardController::class, 'createTask']);
    Route::get('/edit/{id}', [DashboardController::class, 'editTask']);
    Route::put('/update/{id}', [DashboardController::class, 'updateTask']);
    Route::delete('/delete/{id}', [DashboardController::class, 'deleteTask']);
});


/*--------------------------------------------------------------------------
| Priority                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser','can:Modulo_Dashboard'])->prefix('Priority')->group(function () {
    Route::get('/', [PriorityController::class, 'priorities'])->name('universidades'); //
    Route::get('/get', [PriorityController::class, 'get']);
});


/*--------------------------------------------------------------------------
| Status                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser','can:Modulo_Dashboard'])->prefix('Status')->group(function () {
    Route::get('/', [StatusController::class, 'status'])->name('universidades'); //
    Route::get('/get', [StatusController::class, 'get']);
});

/*--------------------------------------------------------------------------
| Type of Task                                                             |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser','can:Modulo_Dashboard'])->prefix('Types')->group(function () {
    Route::get('/', [TypesController::class, 'types'])->name('universidades'); //
    Route::get('/get', [TypesController::class, 'get']);
});

/*--------------------------------------------------------------------------
| Category                                                                  |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser','can:Modulo_Dashboard'])->prefix('Categories')->group(function () {
    Route::get('/', [CategoryController::class, 'categories'])->name('universidades'); //
    Route::get('/get', [CategoryController::class, 'get']);
});



/*--------------------------------------------------------------------------
| Usuarios                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Dashboard'])->prefix('Users')->group(function () {
    Route::get('/', [UserController::class, 'usuarios'])->name('usuarios');
    Route::get('/get', [UserController::class, 'get']);
    /* Route::post('/agregarUsuario', [UserController::class, 'agregarUsuario']);
    Route::get('/detalleUsuario/{id}', [UserController::class, 'detalleUsuario']);
    Route::put('/editarUsuario', [UserController::class, 'editarUsuario']);
    Route::delete('/eliminarUsuario/{id}', [UserController::class, 'eliminarUsuario']);
    Route::post('/asignarRoles', [UserController::class, 'asignarRoles']);
    Route::get('/obtenerRolesUsuario/{idUser}', [UserController::class, 'obtenerRolesUsuario']); */
});



Route::middleware(['auth', 'can:Modulo_Evento'])->group(function () {




/*--------------------------------------------------------------------------
| Resultados                                                                |
|--------------------------------------------------------------------------*/
    Route::get('/calificaciones', [CalificacionesController::class, 'calificaciones'])->name('calificaciones');
    Route::get('/obtenerResultados', [CalificacionesController::class, 'obtenerResultados']);
    Route::get('obtenerCalificacionesPorJuez', [CalificacionesController::class, 'obtenerCalificacionesPorJuez']);
    Route::get('/obtenerEventosResultados', [CalificacionesController::class, 'obtenerEventosResultados']);
    Route::get('/obtenerJueces', [CalificacionesController::class, 'obtenerJueces']);
    Route::post('/obtenerCalificaciones', [CalificacionesController::class, 'obtenerCalificaciones']);
    Route::post('/publicarGanadores', [CalificacionesController::class, 'publicarGanadores']);

});




Route::middleware(['auth', 'can:Modulo_Api'])->group(function () {

/*--------------------------------------------------------------------------
| Tablas                                                                   |
|--------------------------------------------------------------------------*/
    Route::get('/tablas', [TablasController::class, 'tablas'])->name('tablas');
    Route::get('/obtenerTablas', [TablasController::class, 'obtenerTablas']);
    //Route::post('/crearTabla', [TablasController::class, 'crearTabla']);
    Route::post('/agregarTabla', [TablasController::class, 'agregarTabla']);
    Route::get('/detalleTabla/{id}', [TablasController::class, 'detalleTabla']);
    Route::put('/editarTabla', [TablasController::class, 'editarTabla']);
    Route::delete('/eliminarTabla/{id}', [TablasController::class, 'eliminarTabla']);

/*--------------------------------------------------------------------------
| Accesos                                                                  |
|--------------------------------------------------------------------------*/
    Route::get('/accesos', [AccesosController::class, 'accesos'])->name('accesos');
    Route::get('/obtenerAccesos', [AccesosController::class, 'obtenerAccesos']);
    Route::get('/obtenerAccesoTablas/{id}', [AccesosController::class, 'obtenerAccesoTablas']);
    Route::post('/agregarAcceso', [AccesosController::class, 'agregarAcceso']);
    Route::get('/detalleAcceso/{id}', [AccesosController::class, 'detalleAcceso']);
    Route::put('/editarAcceso', [AccesosController::class, 'editarAcceso']);
    Route::delete('/eliminarAcceso/{id}', [AccesosController::class, 'eliminarAcceso']);
});


/*--------------------------------------------------------------------------
| Evaluación                                                                |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'can:Modulo_Evaluacion'])->group(function () {
    Route::get('/evaluacion/{idProyecto}', [EvaluacionController::class, 'index'])->name('evaluacion');
    Route::get('/obtenerInfoProyecto/{idProyecto}', [EvaluacionController::class, 'obtenerInfoProyecto']);
    Route::get('/obtenerPreguntasPublicadas/{idProyecto}', [EvaluacionController::class, 'obtenerPreguntasPublicadas']);
    Route::post('/guardarCalificacionesReactivos', [EvaluacionController::class, 'guardarCalificaciones']);
    Route::get('/evaluacionDashboard', [EvaluacionController::class, 'evaluacionDashboard'])->name('evaluacionDashboard');
    Route::post('/obtenerProyectosRevisados', [EvaluacionController::class, 'obtenerProyectosRevisados']);
    Route::get('/obtenerProyectosActivos/{idEvento}', [EvaluacionController::class, 'obtenerProyectosActivos']);
    Route::get('/obtenerEventoNombreFecha/{idEvento}', [EvaluacionController::class, 'obtenerEventoNombreFecha']);
    Route::get('/empates/{idEvento}', [EvaluacionController::class, 'empates'])->name('empates');
    Route::get('/obtenerProyectosConEmpate/{idEvento}', [EvaluacionController::class, 'obtenerProyectosConEmpate']);
    Route::post('/registrarVotos', [EvaluacionController::class, 'registrarVotos']);
});



Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Utilidades'])->group(function () {

/*--------------------------------------------------------------------------
| Roles                                                                    |
|--------------------------------------------------------------------------*/
    Route::get('/roles', [RoleController::class, 'roles'])->name('roles'); //
    Route::get('/obtenerRoles', [RoleController::class, 'obtenerRoles']);
    Route::post('/agregarRol', [RoleController::class, 'agregarRol']);
    Route::get('/detalleRol/{id}', [RoleController::class, 'detalleRol']);
    Route::put('/editarRol', [RoleController::class, 'editarRol']);
    Route::delete('/eliminarRol/{id}', [RoleController::class, 'eliminarRol']);
    Route::post('/asignarpermisos', [RoleController::class, 'asignarpermisos']);
    Route::get('/obtenerPermisosRol/{idRol}', [RoleController::class, 'obtenerPermisosRol']);


/*--------------------------------------------------------------------------
| Permisos                                                                 |
|--------------------------------------------------------------------------*/
    Route::get('/permisos', [PermissionController::class, 'permisos'])->name('permisos');
    Route::get('/obtenerPermisos', [PermissionController::class, 'obtenerPermisos']);
    Route::post('/agregarPermisos', [PermissionController::class, 'agregarPermisos']);
    Route::get('/detallePermiso/{id}', [PermissionController::class, 'detallePermiso']);
    Route::put('/editarPermiso', [PermissionController::class, 'editarPermiso']);
    Route::delete('/eliminarPermiso/{id}', [PermissionController::class, 'eliminarPermiso']);


/*--------------------------------------------------------------------------
| Respaldos                                                                 |
|--------------------------------------------------------------------------*/
    Route::get('/respaldos', [RespaldoController::class, 'respaldos'])->name('respaldos');
    Route::get('/obtenerRespaldos', [RespaldoController::class, 'obtenerRespaldos']);
    Route::post('/confirmarPassword', [RespaldoController::class, 'confirmarPassword']);
    Route::post('/realizarRespaldo', [RespaldoController::class, 'realizarRespaldo']);
    Route::post('/descargarRespaldo/{id}', [RespaldoController::class, 'descargarRespaldo']);
    Route::delete('/eliminarRespaldo/{id}', [RespaldoController::class, 'eliminarRespaldo']);

});


/*--------------------------------------------------------------------------
| Logs                                                                      |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'can:Modulo_Log'])->group(function () {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('logs');
});


/*--------------------------------------------------------------------------
| Perfil                                                                    |
|--------------------------------------------------------------------------*/
Route::post('/actualizarInfoUser', [UserController::class, 'actualizarInfoUser']);
Route::get('/obtenerInfoUser', [UserController::class, 'obtenerInfoUser']);
Route::get('/cambiarPassword', [UserController::class, 'password'])->middleware('auth')->name('password');
Route::post('/actualizarPassword', [UserController::class, 'actualizarPassword'])->middleware('auth');

/*--------------------------------------------------------------------------
| Cerrar Sesión                                                             |
|--------------------------------------------------------------------------*/
Route::post('/logout', [UserController::class, 'destroy'])->middleware('auth')->name('logout');
