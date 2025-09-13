<?php

declare(strict_types= 1);

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Exception;

class VettingoBucket {
    private S3Client $s3Client;
    private string $bucket;

    public function __construct() {
        $this->bucket = ENV['DO_SPACES_BUCKET'];

        $this->s3Client = new S3Client([
            'region'  => ENV['DO_SPACES_REGION'] ?? 'us-east-1',
            'endpoint' => ENV['DO_SPACES_ENDPOINT'] ?? 'https://nyc3.digitaloceanspaces.com',
            'use_path_style_endpoint' => true,
            'credentials' => [
                    'key'    => ENV['DO_SPACES_ACCESS_KEY_ID'],
                    'secret' => ENV['DO_SPACES_SECRET_ACCESS_KEY'],
                ],
        ]);
    }

    public function upload(string $pathFilename, string $decodedImg): ?string
    {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $pathFilename,
                'Body'   => $decodedImg,
                'ACL'    => 'public-read',
                'ContentType' => 'image/' . pathinfo($pathFilename, PATHINFO_EXTENSION),
            ]);

            return $result['ObjectURL'] ?? null;

        } catch (AwsException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function delete(string $pathFilename): bool {
        $stringToRemove = ENV['DO_SPACES_ENDPOINT'] . '/' . ENV['DO_SPACES_BUCKET'] . '/';
        $filename = str_replace($stringToRemove,"", $pathFilename);

        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $filename,
            ]);
            return true;
        } catch (AwsException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}