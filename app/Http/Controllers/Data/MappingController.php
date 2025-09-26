<?php

// app/Http/Controllers/MappingController.php
namespace App\Http\Controllers\Data;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class MappingController extends Controller
{
    public function getColumns(Request $request): JsonResponse
    {
        $fromTable = $request->query('from');
        $toTable   = $request->query('to');

        if (!$fromTable || !$toTable) {
            return response()->json(['error' => 'Missing table names (from, to)'], 400);
        }

        // Validate: check if tables exist in DB
        if (!Schema::hasTable($fromTable) || !Schema::hasTable($toTable)) {
            return response()->json(['error' => 'One or both tables do not exist'], 404);
        }

        // Get column names
        $fromColumns = Schema::getColumnListing($fromTable);
        $toColumns   = Schema::getColumnListing($toTable);

        // Exclude system fields
        $exclude = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $fromColumns = array_values(array_diff($fromColumns, $exclude));
        $toColumns   = array_values(array_diff($toColumns, $exclude));

        return response()->json([
            'from_table' => $fromTable,
            'to_table'   => $toTable,
            'from_columns' => $fromColumns,
            'to_columns'   => $toColumns,
        ]);
    }

    // Save or update mapping
    public function saveMapping(Request $request): JsonResponse
    {
        $request->validate([
            'from_table' => 'required|string',
            'to_table'   => 'required|string',
            'mapping'    => 'required|array'
        ]);

        $mapping = FieldMapping::updateOrCreate(
            ['from_table' => $request->from_table, 'to_table' => $request->to_table],
            ['mapping' => $request->mapping]
        );

        return response()->json(['success' => true, 'mapping' => $mapping->mapping]);
    }
}
