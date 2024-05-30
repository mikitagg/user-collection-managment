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
            "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC5mBuKcMLAmRJW\n9m+tQARox9gS8DSK3iKLH7nXAcaZcKdEzxHwvOPZv/cAff1PE1JfovlQxnJUtTFi\nkcobae4ALyZuknNg1xOxnG5UVhm5JhX5+RZLHhJI1bO1HSDEpcS/GOWCTbWhI4+T\nRFtg/bDIQxIk1Q5Y3QWHi1sgseSasjdZz92Sy5TyD0WZjFttumfNlSvFTwViPXoh\nvm9OPfK3fjUPr3/DLocMRmxjS4StjcTlR3/rbNGKIF0F6ES+YiC984f9wrQ9MG6b\nUtLnIh9zqDTeKBTeC6MX90KkRRJEJ6jemLhCJnUa4ran3F/9n+vHFAb4UgV/3e8D\n+tFYmsH1AgMBAAECggEAAx5t7WL9DYTaV2Ecz1SGvnFdTYmBLd7ocF4VdBTbjt3j\nHGI+cu/kWaxtgjHxJ9Bd+gLLHchiniR4B/1bNXVk7SvdrCNZ5661mK6ATCUw7s5C\nYEkHTuEv0s7Z45lHC7GddM3+TV+bmxxLNWEDXXCQn/eV6SM2HS2EV5rH6pdoDWbE\no8XRWduJkyCfYwcFhmLIKtnRRUZ0vc/pn0hmYJf4SXr33RWvEaYjNROdjwUo/n72\nO1LLoehwiinPY1BAUv6O4Q2UqroEKYwi7ced8Q4LwLLRfqw8jQ0TU8IuFCAYPeM5\nqWO1RusU5Y8XvEGwRcSPChdxjjMiXJiL4RdIvH2joQKBgQDcVvrtg25eA4/wIW3h\ncDcYs+O01QH1D6wYkA9oUKJjJ4Nx8K+cuykAKc6fiBeI2LaYBjbV3whqJx3v9iRc\nBoW/pdRYOO3zUFGuXi6jblnc9qGDseqtwAab+vqt0MYNtXrfJT9SqsBUQXhwcTY2\nd22P1i8umKYnQ43lwu+/rWZClwKBgQDXoZAuvaWPQugf7uGaNJvR0fLUv2VjoXKF\nFnuA3v/FxcUkbQ0tkaio2XGb5RvOWh6Zb0Ciwa7WWwZniBnX2PkTs+iMrPZ76bOP\ncdtq0lqq6nPlg4v+rrAFD083AIzhEyNI9hYkhBmeh+gdJPZPdjJ7P1kmZ0Jcv6ps\nq4r0RyqNUwKBgGir1UZRgnOc45rido16vmG4yzpTWjutd0av4PHgJFRQKAxPl00w\nR/jw41w7zVpQAOXVReVxr1CmRn2BA6LH4m+5eldyq+9DP5pC2Yr/2Ca07uNd+KsT\nptAgeUdq/zrx9G3fBubhgL5F384iqetT7rM+v2k2UPAkEluMsFHxMLv/AoGBALLC\nyuAk8qrYwc0vYbJQaNlCXzjGT0yQXHs+zzZgKNSRh0USbvlXJuaMFaYzAqFIjtOO\nqpwDS2mDXsTXzOzngF44KNLu1QyXCvghYNCnAOluopQkQRvrQBuBvJ8RgrlY/0iO\nUn8FUKPfcasvqE7p4yHPu52dvyNa9fNPJ0wp73nnAoGBAIk0cC+SAeIoHmdEJrNv\np6xrGUnQG60hY8EuwL1VNiRTNhfu8CWkJmfoQNrVnZ8vf2ZXyMMo0E558YzLrLrC\nmm/ilHvhWm8d1jCLtKUVB/XmuAIM7MDtRRKB8NUzFCadnij1r1D+afd4SU07MRoS\nmsc6Nb9YdVkrg1I2P+WVoUgG\n-----END PRIVATE KEY-----\n",
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