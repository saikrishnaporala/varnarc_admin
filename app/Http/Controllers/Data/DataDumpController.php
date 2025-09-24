<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\GoogleDriveService;
use App\Http\Services\ImportService;
use App\Models\DriveFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;


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
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0); // no time limit
        set_time_limit(0);

        $request->validate([
            'file_url'   => 'required|url',
            'if_exists'  => 'nullable|in:append,replace',
            'file_type'  => 'nullable|string|in:auto,csv,xlsx,xls',
        ]);

        try {
            $ifExists = $request->if_exists ?? 'append';
            $fileType = $request->file_type ?? 'auto';
            $fileUrl  = $request->file_url;
            $imported = [];
            // ✅ Check if folder
            if (str_contains($fileUrl, '/folders/')) {
                $folderId = $this->extractFolderId($fileUrl);
                $files = $this->driveService->listFilesRecursive($folderId);

                foreach ($files as $file) {
                    $this->importFile($file, $imported);
                }
            } else {
                $fileId = $this->extractFileId($fileUrl);
                $file = $this->driveService->fetchFile($fileId);
                $stat = $this->importFile($file, $imported);
                return "Result : ".$stat;
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
        try {
            Log::info("Starting XLSX → CSV conversion for file: {$filePath}");

            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if ($ext === 'xlsx') {
                $reader = new Xlsx();
            } elseif ($ext === 'xls') {
                $reader = new Xls();
            } elseif ($ext === 'csv') {
                $reader = new Csv();
            } else {
                throw new \Exception("Unsupported file extension: $ext");
            }

            // Use Xlsx reader directly for large files
            // $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            Log::info("reader XLSX → CSV conversion for file");
            $reader->setReadEmptyCells(false); // save memory
            Log::info("reader1 XLSX → CSV conversion for file");
            $spreadsheet = $reader->load($filePath);
            Log::info("reader2 XLSX → CSV conversion for file");
            $csvPath = $filePath . ".csv";
            Log::info("Loaded XLSX → CSV conversion for file: {$csvPath}");
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setSheetIndex(0); // first sheet only
            $writer->save($csvPath);

            // Free memory
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            Log::info("Conversion successful. CSV saved to: {$csvPath}");
            return $csvPath;

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Log::error("PhpSpreadsheet read error: " . $e->getMessage());
            throw new \Exception("Failed to convert XLSX to CSV: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Conversion error: " . $e->getMessage());
            throw new \Exception("Failed to convert XLSX to CSV: " . $e->getMessage());
        }
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

    private function importFile(array $file, array &$imported)
    {
        try {
            if (!in_array($file['mime'], [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
                'application/vnd.google-apps.spreadsheet'
            ])) {
                return "error"; // skip unsupported
            }

            // ✅ Detect extension
            $extension = match ($file['mime']) {
                'application/vnd.ms-excel' => 'xls',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'text/csv' => 'csv',
                'application/vnd.google-apps.spreadsheet' => 'csv',
                default => 'dat',
            };

            // ✅ Download file
            $tempPath = storage_path("app/temp_{$file['id']}.{$extension}");
            $downloadedPath = $this->driveService->downloadFile($file['id'], $tempPath);
            Log::info("Download completed for file: {$file['name']}. Saved to: {$downloadedPath}");

            // ✅ Convert to CSV if needed
            if (in_array($file['mime'], [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])) {
                Log::info("Converting XLS/XLSX to CSV for file: {$file['name']}");
                $csvPath = $this->convertXlsxToCsv($downloadedPath);
                $downloadedPath = $csvPath;
                Log::info("Conversion completed. CSV saved to: {$csvPath}");
            }

            // ✅ Generate table name
            $tableName = $this->makeTableName($file['name']);

            Log::info("Table name: {$tableName}");

            // ✅ Import file into DB
            $processStat = $this->importService->processLargeCsv($downloadedPath, $tableName, 'append');

            Log::info("Table name: {$tableName}");
            // ✅ Mark success
            $stat = DriveFile::updateOrCreate(
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

            // $imported[] = $file['name'];
            // unlink($downloadedPath); // cleanup
            return $processStat ." test : ".$stat;
        } catch (\Exception $e) {
            // ✅ Log failed file
            $stat = DriveFile::updateOrCreate(
                ['file_id' => $file['id']],
                [
                    'name' => $file['name'],
                    'url' => "https://drive.google.com/file/d/{$file['id']}/view",
                    'mime' => $file['mime'],
                    'status' => 'error: ' . $e->getMessage(),
                ]
            );
            Log::error("Import failed for file: {$file['name']}. Error: " . $e->getMessage());
            return "Import failed for file: {$file['name']}. Error: " . $e->getMessage(). " Drive File Stat : ".$stat;
        }
    }
}
