<?php

namespace MyDrinks\Infrastructure\Application\Filesystem;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem as FlyFileSystem;
use MyDrinks\Application\Filesystem;

final class FlySystemAdapter implements Filesystem
{
    /**
     * @var FlyFileSystem
     */
    private $filesystem;

    /**
     * @param FlyFileSystem $filesystem
     */
    public function __construct(FlyFileSystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Checks if file or directory exists.
     *
     * @param $path
     * @return bool
     */
    public function has($path)
    {
        return $this->filesystem->has($path);
    }

    /**
     * Write content into file. If file exists it will be overwritten.
     *
     * @param $path
     * @param $contents
     */
    public function write($path, $contents)
    {
        if ($this->filesystem->has($path)) {
            $this->remove($path);
        }

        $this->filesystem->write($path, $contents);
    }

    /**
     * Get content of file
     *
     * @param $path
     * @return string
     */
    public function read($path)
    {
        return $this->filesystem->read($path);
    }

    /**
     * Remove file or a directory
     *
     * @param $path
     * @return void
     */
    public function remove($path)
    {
        if (!$this->filesystem->has($path)) {
            return ;
        }

        $meta = $this->filesystem->getMetadata($path);
        if ($meta['type'] === 'file'){
            $this->filesystem->delete($path);
        } else {
            $this->filesystem->deleteDir($path);
        }
    }

    /**
     * @param string $path
     * @return int
     */
    public function foldersCount($path)
    {
        $files = $this->filesystem->listContents($path);
        $count = 0;
        foreach ($files as $file) {
            if ($file['type'] === 'dir') {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * @param string $path
     * @return array
     */
    public function foldersNames($path)
    {
        $files = $this->filesystem->listContents($path);
        $names = [];
        foreach ($files as $file) {
            if ($file['type'] === 'dir') {
                $names[] = $file['filename'];
            }
        }

        return $names;
    }
}
