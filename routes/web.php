<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

// Utility routes MUST go before the {any} route
Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "All caches cleared!";
});

Route::get('/run-composer/{cmd}', function ($cmd) {
    if (request('key') !== env('APP_SECRET_KEY')) {
        abort(403, 'Unauthorized');
    }

    $allowed = ['install', 'update', 'dump-autoload'];

    if (!in_array($cmd, $allowed)) {
        return "Command not allowed!";
    }

    $output = [];
    $status = null;

    exec("composer {$cmd} 2>&1", $output, $status);

    return response()->json([
        'command' => "composer {$cmd}",
        'status' => $status,
        'output' => $output,
    ]);
});

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
