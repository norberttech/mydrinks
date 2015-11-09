<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Imagine\Binary\Loader;

use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

final class HttpLoader implements LoaderInterface
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
     * @param MimeTypeGuesserInterface  $mimeTypeGuesser
     * @param ExtensionGuesserInterface $extensionGuesser
     */
    public function __construct(
        MimeTypeGuesserInterface $mimeTypeGuesser,
        ExtensionGuesserInterface $extensionGuesser
    ) {
        $this->mimeTypeGuesser = $mimeTypeGuesser;
        $this->extensionGuesser = $extensionGuesser;
    }

    /**
     * {@inheritDoc}
     */
    public function find($path)
    {
        if (false !== strpos($path, '../')) {
            throw new NotLoadableException(sprintf("Source image was searched with '%s' out side of the defined root path", $path));
        }

        if (false == @getimagesize($path)) {
            throw new NotLoadableException(sprintf('Source image not found in "%s"', $path));
        }

        $tmpFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($path);
        
        file_put_contents($tmpFilePath, file_get_contents($path));
        
        $mimeType = $this->mimeTypeGuesser->guess($tmpFilePath);

        unlink($tmpFilePath);
        
        return new Binary(
            file_get_contents($path),
            $mimeType,
            $this->extensionGuesser->guess($mimeType)
        );
    }
}