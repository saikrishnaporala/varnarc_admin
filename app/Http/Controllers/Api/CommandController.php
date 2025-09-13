<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        $table = $request->input('table');

        // Example: run migrate for a specific table (or all if not provided)
        if ($table) {
            $exitCode = Artisan::call('migrate', [
                '--path' => "database/migrations/{$table}.php",
                '--force' => true,
            ]);
        } else {
            $exitCode = Artisan::call('migrate', ['--force' => true]);
        }

        return response()->json([
            'output' => Artisan::output(),
            'exitCode' => $exitCode,
        ]);
    }
}
