<?php

namespace MyDrinks\Application;

interface Filesystem 
{
    /**
     * Checks if file or directory exists.
     *
     * @param $path
     * @return bool
     */
    public function has($path);

    /**
     * Write content into file. If file exists it will be overwritten.
     *
     * @param $path
     * @param $contents
     */
    public function write($path, $contents);

    /**
     * Get content of file
     *
     * @param $path
     * @return string
     */
    public function read($path);

    /**
     * Remove file or a directory
     *
     * @param $path
     * @return void
     */
    public function remove($path);

    /**
     * @param string $path
     * @return int
     */
    public function foldersCount($path);

    /**
     * @param $path
     * @return array
     */
    public function foldersNames($path);
}