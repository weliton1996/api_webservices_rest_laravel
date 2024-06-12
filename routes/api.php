<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Método resource cria os métodos index, store, create, show, update, destroy e edit
// Route::resource('cliente', 'App\http\Controllers\ClienteController');

//Método resource cria somente os métodos index, store, show, update e destroy
Route::apiResource('cliente', 'App\http\Controllers\ClienteController');

Route::apiResource('carro', 'App\http\Controllers\CarroController');
Route::apiResource('locacao', 'App\http\Controllers\LocacaoController');
Route::apiResource('marca', 'App\http\Controllers\MarcaController');
Route::apiResource('Modelo', 'App\http\Controllers\ModeloController');


