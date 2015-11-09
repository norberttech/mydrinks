<?php

namespace MyDrinks\Tests\Integration\Infrastructure\Application\Recipe;

use Cocur\Slugify\Slugify;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MyDrinks\Application\Recipe\Image;
use MyDrinks\Application\Serializer;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Infrastructure\Application\Filesystem\FlySystemAdapter;
use MyDrinks\Infrastructure\Application\Recipe\FilesystemImageStorage;
use MyDrinks\Infrastructure\Application\Recipe\JsonFilesystemStorage;
use MyDrinks\Infrastructure\UserInterface\Serializer\JsonSerializer;
use MyDrinks\Infrastructure\UserInterface\SlugGenerator\SlugifyAdapter;

class FilesystemImageStorageTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @var Serializer
     */
    private $serializer;
    
    /**
     * @var FilesystemImageStorage
     */
    private $storage;

    public function setUp()
    {
        $flysystem = new Filesystem(new Local(realpath(MY_DRINKS_VAR_DIR)));
        $flysystem->deleteDir('tmp');
        
        $filesystem = new FlySystemAdapter(new Filesystem(new Local(MY_DRINKS_VAR_DIR . '/tmp')));
        $slugGenerator = new SlugifyAdapter(new Slugify());
        $this->serializer = new JsonSerializer();
        $this->storage = new FilesystemImageStorage($filesystem, $slugGenerator);
    }
    
    public function test_saving_recipe_in_storage()
    {
        $image = new Image("image.jpg", "image content");
        $this->storage->saveImageFor($image, "screwdriver");

        $fileName = 'screwdriver' .
            DIRECTORY_SEPARATOR . implode(str_split(hash('sha256', 'screwdriver'), 2), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . 'screwdriver.jpeg';

        $this->assertStringEqualsFile(MY_DRINKS_VAR_DIR . '/tmp/' . $fileName, "image content");
        $this->assertTrue($this->storage->hasImageFor("screwdriver"));
        $this->assertSame($this->storage->getPathFor("screwdriver"), $fileName);
    }
    
    function tearDown()
    {
        $flysystem = new Filesystem(new Local(MY_DRINKS_VAR_DIR));
        $flysystem->deleteDir('tmp');
    }
}
