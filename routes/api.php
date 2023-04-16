<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\EquipmentsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/equipment/{id}', [EquipmentsController::class, 'show']);
Route::get('/equipment', [EquipmentsController::class, 'index']);
Route::post('/equipment', [EquipmentsController::class, 'create']);
Route::put('/equipment/{id}', [EquipmentsController::class, 'update']);
Route::delete('/equipment/{id}', [EquipmentsController::class, 'remove']);
Route::get('/equipment-type', [EquipmentsController::class, 'getEquipmentTypes']);
