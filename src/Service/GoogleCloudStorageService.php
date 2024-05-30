<?php

namespace App\Service;

use Google\Cloud\Storage\StorageClient;

class GoogleCloudStorageService
{
    private $storageClient;
    private $bucketName;

    public function __construct(string $projectId, string $bucketName)
    {
        $this->storageClient = new StorageClient([
            'keyFilePath' => '/etc/secrets/bold-column-424118-i9-9d1578163f20.json',
        ]);
        $this->bucketName = $bucketName;
    }

    public function uploadImage($file, $fileName)
    {
        $bucket = $this->storageClient->bucket($this->bucketName);
        $object = $bucket->upload(
            fopen($file->getPathname(), 'r'),
            [
                'name' => $fileName
            ]
        );

        return $object->info()['mediaLink'];
    }

    public function getImageUrl($fileName)
    {
        $bucket = $this->storageClient->bucket($this->bucketName);
        $object = $bucket->object($fileName);

        if ($object->exists()) {
            return $object->signedUrl(new \DateTime('tomorrow'));
        }

        return null;
    }
}