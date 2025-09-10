<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\GoogleDriveService;

class GoogleDriveController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function download(Request $request)
    {
        $request->validate([
            'file_id' => 'required|string'
        ]);

        $tempPath = storage_path("app/temp_import.xlsx");
        $this->driveService->downloadFile($request->file_id, $tempPath);

        return response()->json([
            'status' => 'success',
            'file_path' => $tempPath,
        ]);
    }
}
