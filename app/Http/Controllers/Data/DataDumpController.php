<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\GoogleDriveService;
use App\Http\Services\ImportService;
use App\Models\DriveFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class DataDumpController extends Controller
{
    protected $driveService;
    protected $importService;

    public function __construct(GoogleDriveService $driveService, ImportService $importService)
    {
        $this->driveService = $driveService;
        $this->importService = $importService;
    }

    // List all Drive Files
    public function index()
    {
        return response()->json(DriveFile::all());
    }

    public function importFromGoogleDrive(Request $request)
    {
        ini_set('memory_limit', '512M');

        $request->validate([
            'file_url'   => 'required|url',
            'if_exists'  => 'nullable|in:append,replace',
            'file_type'  => 'nullable|string|in:auto,csv,xlsx,xls',
        ]);

        try {
            $ifExists = $request->if_exists ?? 'append';
            $fileType = $request->file_type ?? 'auto';
            $fileUrl  = $request->file_url;

            // ✅ Check if folder
            if (str_contains($fileUrl, '/folders/')) {
                $folderId = $this->extractFolderId($fileUrl);
                $files = $this->driveService->listFilesRecursive($folderId);
                $imported = [];

                foreach ($files as $file) {
                    try {
                        if (!in_array($file['mime'], [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv'
                        ])) {
                            continue; // skip PDFs or unsupported
                        }

                        // ✅ Download file locally
                        $tempPath = storage_path("app/temp_{$file['id']}");
                        $downloadedPath = $this->driveService->downloadFile($file['id'], $tempPath);

                        // ✅ If XLSX → convert to CSV
                        if (in_array($file['mime'], [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ])) {
                            $csvPath = $this->convertXlsxToCsv($downloadedPath);
                            $downloadedPath = $csvPath;
                        }

                        // ✅ Generate table name
                        $tableName = $this->makeTableName($file['name']);

                        // ✅ Import file into DB
                        $this->importService->processLargeCsv($downloadedPath, $tableName, 'append');

                        // ✅ Mark success
                        DriveFile::updateOrCreate(
                            ['file_id' => $file['id']],
                            [
                                'name' => $file['name'],
                                'url' => "https://drive.google.com/file/d/{$file['id']}/view",
                                'mime' => $file['mime'],
                                'status' => 'imported',
                                'table_name' => $tableName,
                                'size' => $file['size'] ?? null,
                                'rows' => $file['rows'] ?? null,
                            ]
                        );

                        $imported[] = $file['name'];
                        unlink($downloadedPath); // cleanup

                    } catch (\Exception $e) {
                        // ✅ Log failed file
                        DriveFile::updateOrCreate(
                            ['file_id' => $file['id']],
                            [
                                'name' => $file['name'],
                                'url' => "https://drive.google.com/file/d/{$file['id']}/view",
                                'mime' => $file['mime'],
                                'status' => 'error: ' . $e->getMessage(),
                            ]
                        );
                    }
                }
            } else {
                $fileId = $this->extractFileId($fileUrl);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => "test : " . $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => count($imported) . " files imported",
            'files' => $files,
        ]);
    }

    /**
     * Convert XLSX/XLS to CSV for faster import
     */
    private function convertXlsxToCsv(string $filePath): string
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $csvPath = $filePath . ".csv";
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
        // setDelimiter and setEnclosure are not available on PhpSpreadsheet\Writer\Csv via IOFactory
        // So we need to cast to the correct class before calling them
        if ($writer instanceof Csv) {
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
        }
        $writer->save($csvPath);

        return $csvPath;
    }

    /**
     * Normalize table name from file name
     */
    private function makeTableName(string $fileName): string
    {
        $table = strtolower(pathinfo($fileName, PATHINFO_FILENAME));
        $table = preg_replace('/[^a-z0-9_]/', '_', $table);
        $table = preg_replace('/_+/', '_', $table);
        return substr($table, 0, 64); // MySQL table name length limit
    }

    public function importFromGoogleDrive_old(Request $request)
    {
        ini_set('memory_limit', '512M');

        $request->validate([
            'file_url'   => 'required|url',
            'if_exists'  => 'nullable|in:append,replace',
            'file_type'  => 'nullable|string|in:auto,csv,xlsx,xls',
        ]);

        try {
            $ifExists = $request->if_exists ?? 'append';
            $fileType = $request->file_type ?? 'auto';
            $fileUrl  = $request->file_url;

            // ✅ Check if folder
            if (str_contains($fileUrl, '/folders/')) {
                $folderId = $this->extractFolderId($fileUrl);
                $files = $this->driveService->listFilesRecursive($folderId);

                foreach ($files as $file) {
                    // Save metadata to DB
                    DriveFile::updateOrCreate(
                        ['file_id' => $file['id']],
                        [
                            'name' => $file['name'],
                            'mime' => $file['mime'],
                            'url'  => $file['url'],
                            'size' => $file['size'] ?? null,
                            'status'     => 'pending',
                            'table_name' => '',
                        ]
                    );

                    // Download + import
                    // $tempPath = storage_path("app/temp_import_" . $file['id'] . ".xlsx");
                    // $this->driveService->downloadFile($file['id'], $tempPath);
                    // $this->importService->processFile($tempPath, 'contacts', $ifExists);
                }

                return response()->json([
                    'status'  => 'success',
                    'message' => "Imported " . count($files) . " files from folder",
                ]);
            } else {
                // ✅ Single file
                $fileId = $this->extractFileId($fileUrl);
                // $tempPath = storage_path("app/temp_import." . ($fileType === 'auto' ? 'xlsx' : $fileType));

                // if ($fileId) {
                //     $this->driveService->downloadFile($fileId, $tempPath);

                // Save metadata
                // DriveFile::updateOrCreate(
                //     ['file_id' => $fileId],
                //     [
                //         'name' => basename($tempPath),
                //         'mime' => $fileType,
                //         'url'  => "https://drive.google.com/file/d/{$fileId}/view",
                //         'size' => $file['size'] ?? null,
                //     ]
                // );
                // } else {
                //     $fileContent = file_get_contents($fileUrl);
                //     file_put_contents($tempPath, $fileContent);
                // }

                // $this->importService->processFile($tempPath, 'contacts', $ifExists);

                return response()->json([
                    'status'  => 'success',
                    'message' => "Data imported successfully!",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => "test : " . $e->getMessage(),
            ], 500);
        }
    }

    public function import($id)
    {
        $tempPath = null;
        try {
            // Find file record
            $file = DriveFile::findOrFail($id);

            // Download + import
            // $tempPath = storage_path("app/temp_import_" . $file['file_id'] . ".xlsx");
            // $downloadstat = $this->driveService->downloadFile($file['file_id'], $tempPath);
            // $this->importService->processFile($tempPath, $this->makeTableName($file->name), 'append');

            $tempPath = storage_path("app/temp_import_" . $file['file_id']);
            $tempPath = $this->driveService->downloadFileAsCsv($file['file_id'], $tempPath);
            $processstat = $this->importService->processFile($tempPath, $this->makeTableName($file->name), 'append');

            // Mark file as imported
            $file->status = 'imported';
            $file->save();

            return response()->json([
                'status'  => 'success',
                'message' => "File ".$this->makeTableName($file->name)." imported successfully.",
                'path' => $tempPath,
                'download' => $tempPath,
                'table_name' => $this->makeTableName($file->name),
                'process' => $processstat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'fileid' => $file['file_id']
            ], 500);
        }
        finally {
            // if (file_exists($tempPath)) {
            //     unlink($tempPath);
            // }
        }
    }

    private function extractFileId(string $url): ?string
    {
        if (preg_match('/\/d\/(.*?)\//', $url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/id=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractFolderId(string $url): ?string
    {
        if (preg_match('/\/folders\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    // private function makeTableName(string $filename): string
    // {
    //     // Remove extension
    //     $name = pathinfo($filename, PATHINFO_FILENAME);

    //     // Replace anything not a-z, A-Z, 0-9 with underscore
    //     $name = preg_replace('/[^A-Za-z0-9]+/', '_', $name);

    //     // Trim multiple underscores
    //     $name = preg_replace('/_+/', '_', $name);

    //     // Remove leading/trailing underscores
    //     $name = trim($name, '_');

    //     // Convert to lowercase
    //     return strtolower("Data_".$name);
    // }
}
