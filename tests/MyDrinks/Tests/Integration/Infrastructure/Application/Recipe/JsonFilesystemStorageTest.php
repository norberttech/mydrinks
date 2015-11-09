<?php

namespace MyDrinks\Tests\Integration\Infrastructure\Application\Recipe;

use Cocur\Slugify\Slugify;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MyDrinks\Application\Serializer;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Infrastructure\Application\Filesystem\FlySystemAdapter;
use MyDrinks\Infrastructure\Application\Recipe\JsonFilesystemStorage;
use MyDrinks\Infrastructure\UserInterface\Serializer\JsonSerializer;
use MyDrinks\Infrastructure\UserInterface\SlugGenerator\SlugifyAdapter;

class JsonFilesystemStorageTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @var Serializer
     */
    private $serializer;
    
    /**
     * @var JsonFilesystemStorage
     */
    private $storage;

    public function setUp()
    {
        $flysystem = new Filesystem(new Local(realpath(MY_DRINKS_VAR_DIR)));
        $flysystem->deleteDir('tmp');
        
        $filesystem = new FlySystemAdapter(new Filesystem(new Local(MY_DRINKS_VAR_DIR . '/tmp')));
        $slugGenerator = new SlugifyAdapter(new Slugify());
        $this->serializer = new JsonSerializer();
        $this->storage = new JsonFilesystemStorage($filesystem, $slugGenerator, $this->serializer);
    }
    
    public function test_saving_recipe_in_storage()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $this->storage->save($recipe);
        
        $fileName = DIRECTORY_SEPARATOR . 'screwdriver' .
            DIRECTORY_SEPARATOR . implode(str_split(hash('sha256', 'screwdriver'), 2), DIRECTORY_SEPARATOR) . 
            DIRECTORY_SEPARATOR . 'screwdriver.json';
        
        $this->assertStringEqualsFile(MY_DRINKS_VAR_DIR . '/tmp/' . $fileName, $this->serializer->serialize($recipe));
    }

    /**
     * @expectedException \MyDrinks\Application\Exception\Recipe\RecipeNotFoundException
     */
    public function test_throwing_exception_when_removing_not_existing_recipe()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $this->storage->remove($recipe);
    }


    public function test_removing_recipe_from_storage()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $this->storage->save($recipe);

        $this->storage->remove($recipe);
        $fileName = DIRECTORY_SEPARATOR . 'screwdriver';

        $this->assertFileNotExists(MY_DRINKS_VAR_DIR . '/tmp/' . $fileName, $this->serializer->serialize($recipe));
    }
    
    public function test_fetching_recipe_from_storage_by_name()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $this->storage->save($recipe);
        
        $this->assertEquals($recipe, $this->storage->fetchByName(new Name("Screwdriver")));
    }

    public function test_fetching_recipe_from_storage_by_slug()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $this->storage->save($recipe);

        $this->assertEquals($recipe, $this->storage->fetchBySlug("screwdriver"));
    }
    
    /**
     * @expectedException \MyDrinks\Application\Exception\Recipe\RecipeNotFoundException
     */
    public function test_throwing_exception_when_recipe_with_name_does_not_exists()
    {
        $this->storage->fetchByName(new Name("Screwdriver"));
    }

    /**
     * @expectedException \MyDrinks\Application\Exception\Recipe\RecipeNotFoundException
     */
    public function test_throwing_exception_when_recipe_with_slug_does_not_exists()
    {
        $this->storage->fetchBySlug("screwdriver");
    }

    public function test_count()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));
        $this->storage->save(new Recipe(new Name("Mojito")));

        $this->assertSame(2, $this->storage->count());
    }
    
    public function test_fetch_all()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));
        $this->storage->save(new Recipe(new Name("Mojito")));
        
        $recipes = $this->storage->fetchAll();
        $this->assertSame("Mojito", (string) $recipes->current()->getName());
        $recipes->next();
        $this->assertSame("Screwdriver", (string) $recipes->current()->getName());
    }
    
    function tearDown()
    {
        $flysystem = new Filesystem(new Local(MY_DRINKS_VAR_DIR));
        $flysystem->deleteDir('tmp');
    }
}
