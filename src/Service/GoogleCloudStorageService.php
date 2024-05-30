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
            "type" => "service_account",
            "project_id" => "bold-column-424118-i9",
            "private_key_id" => "9d1578163f205c447c12047b8e8782f27822f8cb",
            "private_key" => getenv('GOOGLE_CLOUD_PRIVATE_KEY'),
            "client_email" => "mikitabondarkou@bold-column-424118-i9.iam.gserviceaccount.com",
            "client_id" => "101092736103622014913",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/mikitabondarkou%40bold-column-424118-i9.iam.gserviceaccount.com",
            "universe_domain" => "googleapis.com"
        ];

        $this->storageClient = new StorageClient([
            'projectId' => "bold-column-424118-i9",
            'keyFile' => $keyFile,
        ]);
        dd(getenv("GOOGLE_CLOUD_PRIVATE_KEY"));
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