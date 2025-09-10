<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Http\Services\GoogleDriveService;
use App\Http\Services\ImportService;

class ContactImportController extends Controller
{
    protected $driveService;
    protected $importService;

    public function __construct(GoogleDriveService $driveService, ImportService $importService)
    {
        $this->driveService = $driveService;
        $this->importService = $importService;
    }

    public function importFromGoogleDrive(Request $request)
    {
        $request->validate([
            'file_url'   => 'required|url',
            'table_name' => 'required|string|max:150',
            'if_exists'  => 'nullable|in:append,replace',
            'file_type'  => 'nullable|string|in:auto,csv,xlsx,xls',
        ]);

        try {
            $tableName = strtolower($request->table_name);
            $ifExists  = $request->if_exists ?? 'append';
            $fileType  = $request->file_type ?? 'auto';
            $fileUrl   = $request->file_url;

            // ✅ Extract Google Drive fileId if it's a Google Drive URL
            $fileId = null;
            if (str_contains($fileUrl, 'drive.google.com') || str_contains($fileUrl, 'docs.google.com')) {

                // Case 1: https://drive.google.com/file/d/FILE_ID/view?usp=sharing
                if (preg_match('/\/d\/(.*?)\//', $fileUrl, $matches)) {
                    $fileId = $matches[1];
                }
                // Case 2: https://drive.google.com/open?id=FILE_ID
                elseif (preg_match('/id=([^&]+)/', $fileUrl, $matches)) {
                    $fileId = $matches[1];
                }
            }

            // ✅ Download file
            $tempPath = storage_path("app/temp_import." . ($fileType === 'auto' ? 'xlsx' : $fileType));

            if ($fileId) {
                // Download from Google API
                $this->driveService->downloadFile($fileId, $tempPath);
            } else {
                // Download from direct URL
                $fileContent = file_get_contents($fileUrl);
                file_put_contents($tempPath, $fileContent);
            }

            // ✅ Import into DB
            $this->importService->processFile($tempPath, $tableName, $ifExists);

            return response()->json([
                'status'  => 'success',
                'message' => "Data imported into table `{$tableName}` successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => "test :".$e->getMessage(),
            ], 500);
        }
    }
}
