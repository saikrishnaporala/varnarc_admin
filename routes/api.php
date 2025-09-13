<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\Data\DataDumpController;

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

Route::apiResource('contacts', ContactController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('datadump', DataDumpController::class);

Route::post('/drive/download', [GoogleDriveController::class, 'download']);
Route::post('/import/google-drive', [DataDumpController::class, 'importFromGoogleDrive']);
Route::post('/datadump/{id}', [DataDumpController::class, 'import']);


use App\Http\Controllers\Api\CommandController;

Route::post('/execute-command', [CommandController::class, 'execute']);
Route::get('/command-logs', [CommandController::class, 'logs']);
