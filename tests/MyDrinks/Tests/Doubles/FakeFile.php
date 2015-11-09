<?php

namespace MyDrinks\Tests\Doubles;

final class FakeFile extends \SplFileInfo
{
    private $fileName;

    /**
     * (PHP 5 &gt;= 5.1.2)<br/>
     * Construct a new SplFileInfo object
     * @link http://php.net/manual/en/splfileinfo.construct.php
     * @param $file_name
     */
    public function __construct($file_name)
    {
        $this->fileName = $file_name;
    }

    /**
     * (PHP 5 &gt;= 5.2.2)<br/>
     * Gets absolute path to file
     * @link http://php.net/manual/en/splfileinfo.getrealpath.php
     * @return string the path to the file.
     */
    public function getRealPath()
    {
        return $this->fileName;
    }
}