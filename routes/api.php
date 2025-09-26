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
Route::get('/data/records/{tableName}', [DataDumpController::class, 'getTableRecords'])->name('data.records');

Route::post('/data/{table}/{id}', [DataDumpController::class, 'addToContact']);
Route::put('/data/{table}/{id}', [DataDumpController::class, 'updateRecord']);
Route::delete('/data/{table}/{id}', [DataDumpController::class, 'deleteRecord']);

use App\Http\Controllers\Data\MappingController;

Route::get('/api/mapping/columns', [MappingController::class, 'getColumns']);
Route::post('/api/mapping/save', [MappingController::class, 'saveMapping']);


use App\Http\Controllers\Api\CommandController;

Route::post('/execute-command', [CommandController::class, 'execute']);
Route::get('/command-logs', [CommandController::class, 'logs']);
