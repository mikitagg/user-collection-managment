<?php

namespace App\Service;

use Google\Cloud\Storage\StorageClient;

class GoogleCloudStorageService
{
    private $storageClient;
    private $bucketName;

    public function __construct()
    {
        $this->storageClient = new StorageClient([
            'projectId' => getenv('GOOGLE_CLOUD_PROJECT_ID'),
            'key' => getenv('GOOGLE_CLOUD_PRIVATE_KEY'),
        ]);
        $this->bucketName = 'mikitaimagebucket';
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