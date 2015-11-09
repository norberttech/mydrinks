<?php

namespace MyDrinks\Tests\Integration\Infrastructure\UserInterface\Serializer;

use MyDrinks\Application\Recipe\Actions;
use MyDrinks\Application\Recipe\Description\TasteBuilder;
use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Description;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use MyDrinks\Infrastructure\UserInterface\Serializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase 
{
    public function test_empty_recipe_serialization()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));

        $expected = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [],
            'glass' => null,
            "description" => [
                "text" => null,
                "IBAOfficial" => false,
                "alcoholContent" => null,
                "taste" => []
            ]
        ]);

        $this->assertJsonStringEqualsJsonString($expected, $serializer->serialize($recipe));
    }

    public function test_recipe_with_description_serialization()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));
        $description = new Description();
        $description->setText("Lorem ipsum");
        $description->markAsIBAOfficial();
        $description->setAlcoholContent(25);
        
        $taste = (new TasteBuilder())->sour()->spicy()->buildTaste();
        $description->changeTaste($taste);
        
        $recipe->updateDescription($description);
        
        $json = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [],
            "glass" => null,
            "description" => [
                "text" => "Lorem ipsum",
                "IBAOfficial" => true,
                "alcoholContent" => 25,
                "taste" => [
                    Tastes::SOUR,
                    Tastes::SPICY
                ]
            ]
        ]);

        $this->assertJsonStringEqualsJsonString($json, $serializer->serialize($recipe));
        $this->assertEquals($recipe, $serializer->deserialize($json, Recipe::class));
    }


    public function test_recipe_pouring_and_shaker_steps_serialization()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Highball"), new Capacity(350));
        $recipe->pourIntoGlass(new Name("Vodka"), new Capacity(50));
        $recipe->prepareTheShaker(new Capacity(350));
        $recipe->pourIntoShaker(new Name("Vodka"), new Capacity(50));
        $recipe->shakeShakerContent();
        $recipe->strainIntoGlassFromShaker();

        $json = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [
                [
                    "type" => Actions::PREPARE_GLASS,
                    "name" => "Highball",
                    "capacity" => 350,
                    "amount" => 1
                ],
                [
                    "type" => Actions::POUR_INTO_GLASS,
                    "name" => "Vodka",
                    "capacity" => 50,
                ],
                [
                    "type" => Actions::PREPARE_SHAKER,
                    "capacity" => 350,
                ],
                [
                    "type" => Actions::POUR_INTO_SHAKER,
                    "name" => "Vodka",
                    "capacity" => 50,
                ],
                [
                    "type" => Actions::SHAKE_SHAKER_CONTENT,
                ],
                [
                    "type" => Actions::STRAIN_INTO_GLASS_FROM_SHAKER,
                ]
            ],
            "glass" => "Highball",
            "description" => [
                "text" => null,
                "IBAOfficial" => false,
                "alcoholContent" => null,
                "taste" => []
            ]
        ]);
        
        $this->assertJsonStringEqualsJsonString($json, $serializer->serialize($recipe));
        $this->assertEquals($recipe, $serializer->deserialize($json, Recipe::class));
    }
    
    public function test_recipe_with_filling_steps()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Highball"), new Capacity(350));
        $recipe->prepareTheShaker(new Capacity(350));
        $recipe->fillGlassWith(new Name("Ice"));
        $recipe->fillShakerWith(new Name("Ice"));

        $json = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [
                [
                    "type" => Actions::PREPARE_GLASS,
                    "name" => "Highball",
                    "capacity" => 350,
                    "amount" => 1
                ],
                [
                    "type" => Actions::PREPARE_SHAKER,
                    "capacity" => 350,
                ],                
                [
                    "type" => Actions::FILL_GLASS,
                    "name" => "Ice",
                ],
                [
                    "type" => Actions::FILL_SHAKER,
                    "name" => "Ice",
                ],
            ],
            "glass" => "Highball",
            "description" => [
                "text" => null,
                "IBAOfficial" => false,
                "alcoholContent" => null,
                "taste" => []
            ]
        ]);

        $this->assertJsonStringEqualsJsonString($json, $serializer->serialize($recipe));
        $this->assertEquals($recipe, $serializer->deserialize($json, Recipe::class));
    }


    public function test_recipe_with_adding_ingredients_steps()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Highball"), new Capacity(350));
        $recipe->prepareTheShaker(new Capacity(350));
        $recipe->addIngredientIntoGlass(new Name("Ice"), new Amount(5));
        $recipe->muddleContent();
        $recipe->addIngredientIntoShaker(new Name("Ice"), new Amount(5));

        $json = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [
                [
                    "type" => Actions::PREPARE_GLASS,
                    "name" => "Highball",
                    "capacity" => 350,
                    "amount" => 1
                ],
                [
                    "type" => Actions::PREPARE_SHAKER,
                    "capacity" => 350,
                ],
                [
                    "type" => Actions::ADD_INGREDIENT_INTO_GLASS,
                    "name" => "Ice",
                    "amount" => 5,
                ],
                [
                    "type" => Actions::MUDDLE_GLASS_CONTENT,
                ],
                [
                    "type" => Actions::ADD_INGREDIENT_INTO_SHAKER,
                    "name" => "Ice",
                    "amount" => 5
                ],
            ],
            "glass" => "Highball",
            "description" => [
                "text" => null,
                "IBAOfficial" => false,
                "alcoholContent" => null,
                "taste" => []
            ]
        ]);

        $this->assertJsonStringEqualsJsonString($json, $serializer->serialize($recipe));
        $this->assertEquals($recipe, $serializer->deserialize($json, Recipe::class));
    }

    public function test_recipe_with_glass_actions_step()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Highball"), new Capacity(350));
        $recipe->pourIntoGlass(new Name("Vodka"), new Capacity(50));
        $recipe->emptyTheGlass();
        $recipe->pourIntoGlass(new Name("Vodka"), new Capacity(50));
        $recipe->stirGlassContent();
        $recipe->igniteGlassContent();
        $recipe->garnishGlass(new Name("straw"));
        
        $json = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [
                [
                    "type" => Actions::PREPARE_GLASS,
                    "name" => "Highball",
                    "capacity" => 350,
                    "amount" => 1
                ],
                [
                    "type" => Actions::POUR_INTO_GLASS,
                    "name" => "Vodka",
                    "capacity" => 50,
                ],
                [
                    "type" => Actions::EMPTY_GLASS_CONTENT,
                ],
                [
                    "type" => Actions::POUR_INTO_GLASS,
                    "name" => "Vodka",
                    "capacity" => 50,
                ],
                [
                    "type" => Actions::STIR_GLASS_CONTENT,
                ],
                [
                    "type" => Actions::IGNITE_GLASS_CONTENT,
                ],
                [
                    "type" => Actions::GARNISH_GLASS,
                    "name" => "straw"
                ],
            ],
            "glass" => "Highball",
            "description" => [
                "text" => null,
                "IBAOfficial" => false,
                "alcoholContent" => null,
                "taste" => []
            ]
        ]);

        $this->assertJsonStringEqualsJsonString($json, $serializer->serialize($recipe));
        $this->assertEquals($recipe, $serializer->deserialize($json, Recipe::class));
    }

    public function test_recipe_with_top_up_step()
    {
        $serializer = new JsonSerializer();

        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Highball"), new Capacity(350));
        $recipe->topUpGlass(new Name("Vodka"));

        $json = json_encode([
            "name" => "Screwdriver",
            "publicationDate" => null,
            "steps" => [
                [
                    "type" => Actions::PREPARE_GLASS,
                    "name" => "Highball",
                    "capacity" => 350,
                    "amount" => 1
                ],
                [
                    "type" => Actions::TOP_UP_GLASS,
                    "name" => "Vodka",
                ],
            ],
            "glass" => "Highball",
            "description" => [
                "text" => null,
                "IBAOfficial" => false,
                "alcoholContent" => null,
                "taste" => []
            ],
        ]);

        $this->assertJsonStringEqualsJsonString($json, $serializer->serialize($recipe));
        $this->assertEquals($recipe, $serializer->deserialize($json, Recipe::class));
    }
}
