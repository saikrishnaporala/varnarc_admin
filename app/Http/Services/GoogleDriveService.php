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

    public function downloadFile(string $fileId, string $tempPath): string
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $this->service->files->get($fileId, ['alt' => 'media']);

        $content = $response->getBody()->getContents();

        file_put_contents($tempPath, $content);

        return $tempPath;
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
            'fields' => 'files(id, name, mimeType)'
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
                ];
            }
        }
    }
    
}
