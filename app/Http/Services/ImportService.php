<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportService
{
    public function processFile(string $filePath, string $tableName, string $ifExists = 'append')
    {
        $rows = Excel::toArray([], $filePath)[0]; // First sheet

        if (empty($rows) || empty($rows[0])) {
            throw new \Exception('No data found in file');
        }

        $columns = $rows[0];
        $normalizedColumns = array_map(fn($col) => str_replace(' ', '_', strtolower(trim($col))), $columns);

        // Drop + recreate table if needed
        if (Schema::hasTable($tableName) && $ifExists === 'replace') {
            Schema::drop($tableName);
        }

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($normalizedColumns) {
                $table->id();
                foreach ($normalizedColumns as $col) {
                    $table->text($col)->nullable();
                }
                $table->timestamps();
            });
        }

        // Insert rows
        foreach (array_slice($rows, 1) as $row) {
            $data = [];
            foreach ($normalizedColumns as $i => $col) {
                $data[$col] = $row[$i] ?? null;
            }
            DB::table($tableName)->insert($data);
        }

        return true;
    }
}
