<?php

namespace App\Service;

use Google\Cloud\Storage\StorageClient;

class GoogleCloudStorageService
{
    private $storageClient;
    private $bucketName;

    public function __construct()
    {
        $keyFile = [
            "type"=> "service_account",
            "project_id"=> getenv('GOOGLE_CLOUD_PROJECT_ID'),
            "private_key_id"=> getenv('GOOGLE_CLOUD_PRIVATE_KEY_ID'),
            "private_key"=> getenv('GOOGLE_CLOUD_PRIVATE_KEY'),
            "client_email"=> getenv('GOOGLE_CLOUD_CLIENT_EMAIL'),
            "client_id"=> getenv('GOOGLE_CLOUD_CLIENT_ID'),
            "auth_uri"=> getenv('GOOGLE_CLOUD_AUTH_URI'),
            "token_uri"=> getenv('GOOGLE_CLOUD_TOKEN_URI'),
            "auth_provider_x509_cert_url"=> getenv('GOOGLE_CLOUD_AUTH_PROVIDER'),
            "client_x509_cert_url"=> getenv('GOOGLE_CLOUD_CERT_URL'),
            "universe_domain"=> "googleapis.com"
        ];

        $this->storageClient = new StorageClient([
            'projectId' => getenv('GOOGLE_CLOUD_PROJECT_ID'),
            'keyFile' => $keyFile,
        ]);
        $this->bucketName = getenv('GOOGLE_CLOUD_STORAGE_BUCKET');
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