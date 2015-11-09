<?php

namespace MyDrinks\Tests\Integration\Infrastructure\Filesystem;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MyDrinks\Infrastructure\Application\Filesystem\FlySystemAdapter;

class FlySystemAdapterTest extends \PHPUnit_Framework_TestCase 
{
    function setUp()
    {
        $flysystem = new Filesystem(new Local(realpath(MY_DRINKS_VAR_DIR)));
        $flysystem->deleteDir('tmp');
    }
    
    function test_write_file()
    {
        $filesystem = $this->createFilesystem();
        
        $filesystem->write("/foo", "foo");
        
        $this->assertStringEqualsFile(MY_DRINKS_VAR_DIR . '/tmp/foo', "foo");
    }

    function test_overwrite_file()
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write("/foo", "foo");
        $filesystem->write("/foo", "bar");

        $this->assertStringEqualsFile(MY_DRINKS_VAR_DIR . '/tmp/foo', "bar");
    }
    
    function test_has_file()
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write("/foo", "foo");
        
        $this->assertTrue($filesystem->has('/foo'));
    }

    function test_delete_file()
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write("/foo", "foo");
        $filesystem->remove('/foo');

        $this->assertFileNotExists(MY_DRINKS_VAR_DIR . '/tmp/foo');
    }
    
    function test_delete_dir_with_content()
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write("/foo/bar", "foo");
        $filesystem->remove('/foo');

        $this->assertFileNotExists(MY_DRINKS_VAR_DIR . '/tmp/foo/bar');
    }

    function test_folders_count()
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write("/foo/bar", "foo");
        $filesystem->write("/bar/bar", "foo");
        $filesystem->write("/bar.txt", "foo");

        $this->assertSame(2, $filesystem->foldersCount("/"));
    }

    function test_folder_names()
    {
        $filesystem = $this->createFilesystem();

        $filesystem->write("/folder1/bar", "foo");
        $filesystem->write("/folder2/bar", "foo");
        $filesystem->write("/bar.txt", "foo");

        $this->assertSame(["folder1", "folder2"], $filesystem->foldersNames("/"));
    }

    private function createFilesystem()
    {
        return new FlySystemAdapter(new Filesystem(new Local(MY_DRINKS_VAR_DIR . '/tmp')));
    }

    function tearDown()
    {
        $flysystem = new Filesystem(new Local(MY_DRINKS_VAR_DIR));
        $flysystem->deleteDir('tmp');
    }
}
