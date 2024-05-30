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
            'keyFile' => [
                    "type"=> "service_account",
                    "project_id"=> getenv('GOOGLE_CLOUD_PROJECT_ID'),
                    "private_key_id"=> getenv('GOOGLE_CLOUD_PRIVATE_KEY_ID'),
                    "private_key"=> getenv('GOOGLE_CLOUD_PRIVATE_KEY'),
                    "client_email"=> "mikitabondarkou@bold-column-424118-i9.iam.gserviceaccount.com",
                    "client_id"=> "101092736103622014913",
                    "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
                    "token_uri"=> "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url"=> getenv('GOOGLE_CLOUD_AUTH_PROVIDER'),
                    "client_x509_cert_url"=> getenv('GOOGLE_CLOUD_CERT_URL'),
                    "universe_domain"=> "googleapis.com"
            ],
            ]);
        $this->bucketName = "mikitaimagebucket";
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