<?php

namespace App\Http\Services;

use Google\Client;
use Google\Service\Drive;

class GoogleDriveService
{
    protected Drive $service;

    public function __construct(Client $client)
    {
        $client->setAuthConfig(storage_path('app/google/varnarc-googleapi.json'));
        $client->addScope(Drive::DRIVE_READONLY);

        $this->service = new Drive($client);
    }

    public function downloadFileAsCsv(string $fileId, string $tempPath): string
    {
        $file = $this->service->files->get($fileId, ['fields' => 'id, name, mimeType']);

        switch ($file->mimeType) {
            case 'application/vnd.google-apps.spreadsheet':
                // ✅ Export Google Sheets as CSV
                $response = $this->service->files->export($fileId, 'text/csv');
                $extension = '.csv';
                break;

            case 'text/csv':
                // ✅ Download CSV file directly
                $response = $this->service->files->get($fileId, ['alt' => 'media']);
                $extension = '.csv';
                break;

            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // XLSX
            case 'application/vnd.ms-excel': // XLS
                // ✅ Download Excel file directly
                $response = $this->service->files->get($fileId, ['alt' => 'media']);
                $extension = '.xlsx';
                break;

            default:
                throw new \Exception("Unsupported file type: {$file->mimeType}");
        }

        $content = $response->getBody()->getContents();

        // ✅ Normalize encoding only for text-based files
        if ($extension === '.csv') {
            $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'], true);
            if ($encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding ?: 'UTF-8');
            }
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        }

        $tempPath = $tempPath . $extension;
        file_put_contents($tempPath, $content);

        return $tempPath;
    }

    // public function downloadFileAsCsv(string $fileId, string $tempPath): string
    // {
    //     try {
    //         // Get file metadata
    //         $file = $this->service->files->get($fileId, ['fields' => 'id, name, mimeType']);

    //         // Decide how to fetch the file
    //         switch ($file->mimeType) {
    //             case 'application/vnd.google-apps.spreadsheet':
    //                 // Export Google Sheet as CSV
    //                 $response = $this->service->files->export($fileId, 'text/csv');
    //                 $extension = '.csv';
    //                 break;

    //             case 'text/csv':
    //                 // Download CSV directly
    //                 $response = $this->service->files->get($fileId, ['alt' => 'media']);
    //                 $extension = '.csv';
    //                 break;

    //             case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // XLSX
    //             case 'application/vnd.ms-excel': // XLS
    //                 // Download Excel file
    //                 $response = $this->service->files->get($fileId, ['alt' => 'media']);
    //                 $extension = '.xlsx';
    //                 break;

    //             default:
    //                 throw new \Exception("Unsupported file type: {$file->mimeType}");
    //         }

    //         // Read response
    //         $content = $response->getBody()->getContents();
    //         if (empty($content)) {
    //             throw new \Exception("Downloaded file is empty or corrupted for file ID: {$fileId}");
    //         }

    //         // Ensure directory exists
    //         $dir = dirname($tempPath);
    //         if (!is_dir($dir)) {
    //             mkdir($dir, 0777, true);
    //         }

    //         // Write file to disk
    //         $finalPath = $tempPath . $extension;
    //         file_put_contents($finalPath, $content);

    //         return $finalPath;

    //     } catch (\Google\Service\Exception $e) {
    //         // Specific Google API errors
    //         throw new \Exception("Google API error while downloading file {$fileId}: " . $e->getMessage(), $e->getCode(), $e);
    //     } catch (\Exception $e) {
    //         // General errors
    //         throw new \Exception("Failed to download file {$fileId}: " . $e->getMessage(), $e->getCode(), $e);
    //     }
    // }



    public function downloadFile(string $fileId, string $tempPath): string
    {
        try {
            // First check the file type
            $file = $this->service->files->get($fileId, ['fields' => 'mimeType']);

            if ($file->getMimeType() === 'application/vnd.google-apps.spreadsheet') {
                // ✅ Export Google Sheet to XLSX
                $response = $this->service->files->export(
                    $fileId,
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ['alt' => 'media']
                );
            } else {
                // ✅ Normal file download (Excel, CSV, etc.)
                $response = $this->service->files->get($fileId, ['alt' => 'media']);
            }

            $content = $response->getBody()->getContents();
            file_put_contents($tempPath, $content);

            // Safety check: ensure it’s not HTML (like a Google login page)
            $head = substr($content, 0, 200);
            if (stripos($head, '<html') !== false) {
                throw new \Exception("Google Drive returned HTML instead of file content. File may not be shared with the service account.");
            }

            return $tempPath;
        } catch (\Exception $e) {
            throw new \Exception("Google Drive download failed: " . $e->getMessage());
        }
    }


    public function listFilesRecursive(string $folderId): array
    {
        $files = [];

        $this->fetchFiles($folderId, $files);

        return $files;
    }

    private function fetchFiles(string $folderId, array &$files): void
    {
        $response = $this->service->files->listFiles([
            'q' => "'" . $folderId . "' in parents and trashed = false",
            'fields' => 'files(id, name, mimeType, size)'
        ]);

        foreach ($response->files as $file) {
            if ($file->mimeType === 'application/vnd.google-apps.folder') {
                // Recursive call for subfolders
                $this->fetchFiles($file->id, $files);
            } else {
                $files[] = [
                    'id'   => $file->id,
                    'name' => $file->name,
                    'mime' => $file->mimeType,
                    'url'  => "https://drive.google.com/file/d/{$file->id}/view", // ✅ add URL
                    'size' => isset($file->size) ? round($file->size / 1024, 2) : null, // ✅ size in bytes
                ];
            }
        }
    }

    
}
