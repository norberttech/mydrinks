<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Imagine\Binary\Loader;

use Aws\S3\S3Client;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

final class S3Loader implements LoaderInterface
{
    /**
     * @var MimeTypeGuesserInterface
     */
    protected $mimeTypeGuesser;

    /**
     * @var ExtensionGuesserInterface
     */
    protected $extensionGuesser;
    
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
     * @var LoaderInterface
     */
    private $fallbackLoader;

    /**
     * @param MimeTypeGuesserInterface  $mimeTypeGuesser
     * @param ExtensionGuesserInterface $extensionGuesser
     */
    public function __construct(
        MimeTypeGuesserInterface $mimeTypeGuesser,
        ExtensionGuesserInterface $extensionGuesser, 
        LoaderInterface $fallbackLoader,
        S3Client $client,
        $bucket,
        $prefix
    ) {
        $this->mimeTypeGuesser = $mimeTypeGuesser;
        $this->extensionGuesser = $extensionGuesser;
        $this->client = $client;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
        $this->fallbackLoader = $fallbackLoader;
    }

    /**
     * {@inheritDoc}
     */
    public function find($path)
    {
        $s3Url = $this->client->getObjectUrl(
            $this->bucket,
            trim($this->prefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR)
        );

        if (false == @getimagesize($s3Url)) {
            return $this->fallbackLoader->find($path);
        }

        $tmpFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($s3Url);
        
        file_put_contents($tmpFilePath, file_get_contents($s3Url));
        
        $mimeType = $this->mimeTypeGuesser->guess($tmpFilePath);

        unlink($tmpFilePath);
        
        return new Binary(
            file_get_contents($s3Url),
            $mimeType,
            $this->extensionGuesser->guess($mimeType)
        );
    }
}