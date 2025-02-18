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

/*--------------------------------------------------------------------------
| Index                                                                    |
|--------------------------------------------------------------------------*/
Route::get('/', [PublicController::class, 'index']); //
Route::get('/Obtenerimagenes/{tipo?}', [ImagesController::class, 'Obtenerimagenes']);
Route::get('/registro', [PublicController::class, 'registro']);


/*--------------------------------------------------------------------------
| Log in                                                                    |
|--------------------------------------------------------------------------*/
Route::get('/login', [UserController::class, 'index']);
Route::post('/login', [UserController::class, 'login'])->name('login');


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
    Route::post('/shareTask', [DashboardController::class, 'shareTask']);
    Route::get('/getU', [DashboardController::class, 'getU']);
    Route::get('/getAssigned/{taskId}', [DashboardController::class, 'getAssigned']); /*  */
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
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Utilidades'])->prefix('Users')->group(function () {
    Route::get('/', [UserController::class, 'usuarios'])->name('usuarios');
    Route::get('/get', [UserController::class, 'get']);
    Route::post('/agregarUsuario', [UserController::class, 'agregarUsuario']);
    Route::get('/detalleUsuario/{id}', [UserController::class, 'detalleUsuario']);
    Route::put('/editarUsuario', [UserController::class, 'editarUsuario']);
    Route::delete('/eliminarUsuario/{id}', [UserController::class, 'eliminarUsuario']);
    Route::post('/asignarRoles', [UserController::class, 'asignarRoles']);
    Route::get('/obtenerRolesUsuario/{idUser}', [UserController::class, 'obtenerRolesUsuario']);
});


/*--------------------------------------------------------------------------
| Roles                                                                    |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Utilidades'])->prefix('Roles')->group(function () {

    Route::get('/', [RoleController::class, 'roles'])->name('roles'); //
    Route::get('/obtenerRoles', [RoleController::class, 'obtenerRoles']);
    Route::post('/agregarRol', [RoleController::class, 'agregarRol']);
    Route::get('/detalleRol/{id}', [RoleController::class, 'detalleRol']);
    Route::put('/editarRol', [RoleController::class, 'editarRol']);
    Route::delete('/eliminarRol/{id}', [RoleController::class, 'eliminarRol']);
    Route::post('/asignarpermisos', [RoleController::class, 'asignarpermisos']);
    Route::get('/obtenerPermisosRol/{idRol}', [RoleController::class, 'obtenerPermisosRol']);
});


Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Utilidades'])->prefix('Permission')->group(function () {
/*--------------------------------------------------------------------------
| Permisos                                                                 |
|--------------------------------------------------------------------------*/
    Route::get('/', [PermissionController::class, 'permisos'])->name('permisos');
    Route::get('/obtenerPermisos', [PermissionController::class, 'obtenerPermisos']);
    Route::post('/agregarPermisos', [PermissionController::class, 'agregarPermisos']);
    Route::get('/detallePermiso/{id}', [PermissionController::class, 'detallePermiso']);
    Route::put('/editarPermiso', [PermissionController::class, 'editarPermiso']);
    Route::delete('/eliminarPermiso/{id}', [PermissionController::class, 'eliminarPermiso']);
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
