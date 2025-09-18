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
        // Export Google Sheets as CSV
        $response = $this->service->files->export($fileId, 'text/csv', ['alt' => 'media']);
        $content = $response->getBody()->getContents();

        file_put_contents($tempPath, $content);

        return $tempPath;
    }

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
