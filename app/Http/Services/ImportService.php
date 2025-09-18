<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Exception;


class ImportService
{
    // public function processFile(string $filePath, string $tableName, string $ifExists = 'append')
    // {
    //     try {
    //         // ✅ Check if file exists
    //         if (!file_exists($filePath)) {
    //             throw new Exception("File not found: {$filePath}");
    //         }

    //         // ✅ Read Excel into array
    //         $sheets = Excel::toArray([], $filePath);
    //         if (empty($sheets) || empty($sheets[0])) {
    //             throw new Exception("No data found in file");
    //         }

    //         $rows = $sheets[0]; // First sheet
    //         $columns = $rows[0] ?? [];

    //         if (empty($columns)) {
    //             throw new Exception("File does not contain header row");
    //         }

    //         // ✅ Normalize column names
    //         $normalizedColumns = array_map(function ($col) {
    //             $col = strtolower(trim($col));
    //             $col = preg_replace('/[^a-z0-9_]/', '_', $col); // keep only [a-z0-9_]
    //             $col = preg_replace('/_+/', '_', $col);         // collapse multiple underscores
    //             $col = trim($col, '_');                         // remove leading/trailing underscores
    //             return $col ?: 'col_' . uniqid();
    //         }, $columns);

    //         // ✅ Ensure no duplicate columns
    //         $uniqueCols = [];
    //         foreach ($normalizedColumns as &$col) {
    //             $original = $col;
    //             $i = 1;
    //             while (in_array($col, $uniqueCols)) {
    //                 $col = $original . '_' . $i;
    //                 $i++;
    //             }
    //             $uniqueCols[] = $col;
    //         }
    //         $normalizedColumns = $uniqueCols;

    //         // ✅ Handle existing table
    //         if (Schema::hasTable($tableName)) {
    //             if ($ifExists === 'replace') {
    //                 Schema::drop($tableName);
    //             }
    //         }

    //         // ✅ Create table dynamically if not exists
    //         if (!Schema::hasTable($tableName)) {
    //             Schema::create($tableName, function (Blueprint $table) use ($normalizedColumns) {
    //                 $table->id();
    //                 foreach ($normalizedColumns as $col) {
    //                     $table->text($col)->nullable();
    //                 }
    //                 $table->timestamps();
    //             });
    //         }

    //         // ✅ Insert rows safely
    //         $insertCount = 0;
    //         foreach (array_slice($rows, 1) as $row) {
    //             $data = [];
    //             foreach ($normalizedColumns as $i => $col) {
    //                 $data[$col] = $row[$i] ?? null;
    //             }

    //             if (!empty(array_filter($data))) { // avoid inserting empty rows
    //                 DB::table($tableName)->insert($data);
    //                 $insertCount++;
    //             }
    //         }

    //         if ($insertCount === 0) {
    //             throw new Exception("No valid rows found to insert into {$tableName}");
    //         }

    //         return [
    //             'status' => 'success',
    //             'message' => "Imported {$insertCount} rows into table `{$tableName}` successfully"
    //         ];
    //     } catch (Exception $e) {
    //         // ✅ Bubble up with clean error
    //         return [
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public function processFile(string $filePath, string $tableName, string $ifExists = 'append')
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception("Unable to open file: {$filePath}");
            }

            $columns = fgetcsv($handle); // first row as headers
            if (empty($columns)) {
                throw new \Exception("File does not contain header row");
            }

            // ✅ Normalize column names
            $normalizedColumns = array_map(function ($col) {
                $col = strtolower(trim($col));
                $col = preg_replace('/[^a-z0-9_]/', '_', $col);
                $col = preg_replace('/_+/', '_', $col);
                return trim($col, '_') ?: 'col_' . uniqid();
            }, $columns);

            // ✅ Ensure no duplicates
            $uniqueCols = [];
            foreach ($normalizedColumns as &$col) {
                $original = $col;
                $i = 1;
                while (in_array($col, $uniqueCols)) {
                    $col = $original . '_' . $i;
                    $i++;
                }
                $uniqueCols[] = $col;
            }
            $normalizedColumns = $uniqueCols;

            // ✅ Handle table existence
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

            // ✅ Insert rows in chunks
            $batch = [];
            $insertCount = 0;
            $chunkSize = 1000;

            while (($row = fgetcsv($handle)) !== false) {
                $data = [];
                foreach ($normalizedColumns as $i => $col) {
                    $data[$col] = $row[$i] ?? null;
                }

                if (!empty(array_filter($data))) {
                    $batch[] = $data;
                }

                if (count($batch) >= $chunkSize) {
                    DB::table($tableName)->insert($batch);
                    $insertCount += count($batch);
                    $batch = [];
                }
            }

            // Insert remaining
            if (!empty($batch)) {
                DB::table($tableName)->insert($batch);
                $insertCount += count($batch);
            }

            fclose($handle);

            if ($insertCount === 0) {
                throw new \Exception("No valid rows found to insert into {$tableName}");
            }

            return [
                'status' => 'success',
                'message' => "Imported {$insertCount} rows into table `{$tableName}` successfully"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
