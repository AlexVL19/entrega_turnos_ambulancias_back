<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* Rutas agrupadas que pertenecen a un controlador en común. */
Route::controller(FormularioController::class)->group(function () {
    Route::post('/addTurnChange', 'addCambioTurno');
    Route::get('/getVerifications', 'getVerifications');
    Route::get('/getResponses', 'getResponses');
    Route::post('/insertIntoBitacora', 'insertIntoBitacora');
    Route::post('insertIntoMain', 'insertIntoMainBitacora');
});

Route::controller(LoginController::class)->group(function () {
    Route::post('/login', 'login');
});
