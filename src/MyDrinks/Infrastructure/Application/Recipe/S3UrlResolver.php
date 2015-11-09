<?php

namespace MyDrinks\Infrastructure\Application\Recipe;

use Aws\S3\S3Client;
use MyDrinks\Application\Recipe\ImageUrlResolver;

final class S3UrlResolver implements ImageUrlResolver
{
    /**
     * @var S3Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $bucket;
    
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param S3Client $client
     * @param string $bucket
     * @param string $prefix
     */
    public function __construct(S3Client $client, $bucket, $prefix)
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }
    
    /**
     * @param $path
     * @return string
     */
    public function resolveUrlFor($path)
    {
        $path = trim($this->prefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);

        return $this->client->getObjectUrl($this->bucket, $path);
    }
}