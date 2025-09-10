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
}
