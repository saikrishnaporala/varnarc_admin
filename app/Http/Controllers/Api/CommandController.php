<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\CommandLog;

class CommandController extends Controller
{
    public function execute(Request $request)
    {
        $table = $request->input('table');

        if ($table) {
            $command = 'migrate';
            $params = [
                '--path' => "database/migrations/{$table}.php",
                '--force' => true,
            ];
        } else {
            $command = 'migrate';
            $params = ['--force' => true];
        }

        $exitCode = Artisan::call($command, $params);
        $output = Artisan::output();

        // Store in DB
        CommandLog::create([
            'command'    => $command,
            'parameters' => $params,
            'exit_code'  => $exitCode,
            'output'     => $output,
            'user_id'    => auth()->id(), // optional
        ]);

        return response()->json([
            'command'   => $command,
            'params'    => $params,
            'exitCode'  => $exitCode,
            'output'    => $output,
        ]);
    }

    public function logs()
    {
        return response()->json(
            CommandLog::latest()->get()
        );
    }
}
