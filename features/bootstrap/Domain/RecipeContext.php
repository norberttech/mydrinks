<?php

namespace Feature\Domain;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Cocur\Slugify\Slugify;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use MyDrinks\Application\Command\AddRecipeStepCommand;
use MyDrinks\Application\Command\CreateNewRecipeCommand;
use MyDrinks\Application\Command\RemoveRecipeStepCommand;
use MyDrinks\Application\Handler\AddRecipeStepHandler;
use MyDrinks\Application\Handler\CreateNewRecipeHandler;
use MyDrinks\Application\Handler\RemoveRecipeStepHandler;
use MyDrinks\Application\Recipe\Actions as RecipeActions;
use MyDrinks\Application\Recipe\Factory\DomainFactory;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Exception\Exception;
use MyDrinks\Domain\Exception\Recipe\GlassCapacityOverflowException;
use MyDrinks\Domain\Exception\Recipe\MissingGlassException;
use MyDrinks\Domain\Exception\Recipe\MissingShakerException;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Name;
use MyDrinks\Infrastructure\Application\CommandBus\Adapter\Tactician;
use MyDrinks\Infrastructure\Application\Recipe\InMemoryRecipes;
use MyDrinks\Infrastructure\UserInterface\SlugGenerator\SlugifyAdapter;

class RecipeContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Name
     */
    private $currentRecipeName;

    /**
     * @var Exception|null
     */
    private $lastException;

    /**
     * @var Tactician
     */
    private $commandBus;

    /**
     * @var SlugifyAdapter
     */
    private $slugGenerator;

    /**
     * @var Recipes
     */
    private $recipes;
    
    public function __construct()
    {
        $this->recipes = new InMemoryRecipes();
        $recipeFactory = new DomainFactory();

        $commandHandlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new InMemoryLocator([
                CreateNewRecipeCommand::class => new CreateNewRecipeHandler($this->recipes, $recipeFactory),
                AddRecipeStepCommand::class => new AddRecipeStepHandler($this->recipes),
                RemoveRecipeStepCommand::class => new RemoveRecipeStepHandler($this->recipes)
            ]),
            new HandleInflector()
        );
        
        $this->commandBus = new Tactician(new CommandBus([$commandHandlerMiddleware]));
        $this->slugGenerator = new SlugifyAdapter(new Slugify());
    }
 
    /**
     * @When I decide to create new recipe called :name
     */
    public function iDecideToCreateNewRecipeCalled($name)
    {
        $command = new CreateNewRecipeCommand();
        $command->name = $name;
        $this->commandBus->handle($command);
        
        $this->currentRecipeName = new Name($name);
    }
    
    /**
     * @When as first step I add: prepare :name glass with :capacity ml capacity
     */
    public function asFirstStepIAddPrepareHighballGlassWithMlCapacity($name, $capacity)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = $this->slugGenerator->generateFrom($this->currentRecipeName);
        $command->type = RecipeActions::PREPARE_GLASS;
        $command->name = $name;
        $command->capacity = (int) $capacity;
        
        $this->commandBus->handle($command);
    }

    /**
     * @When I add step: add :amount :name cubes into glass
     */
    public function iAddStepAddCubesIntoGlass($amount, $name)
    {
        try {
            $command = new AddRecipeStepCommand();
            $command->slug = $this->slugGenerator->generateFrom($this->currentRecipeName);
            $command->type = RecipeActions::ADD_INGREDIENT_INTO_GLASS;
            $command->name = $name;
            $command->amount = (int) $amount;

            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->lastException = $exception;
        }
    }

    /**
     * @When I add step: pour :capacity ml of :name into glass
     */
    public function iAddStepPourMlOfIntoGlass($capacity, $name)
    {
        try {
            $command = new AddRecipeStepCommand();
            $command->slug = $this->slugGenerator->generateFrom($this->currentRecipeName);
            $command->type = RecipeActions::POUR_INTO_GLASS;
            $command->name = $name;
            $command->capacity = (int) $capacity;

            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->lastException = $exception;
        }
    }

    /**
     * @When I add step: pour :capacity ml of :name into shaker
     */
    public function iAddStepPourMlOfIntoShaker($capacity, $name)
    {
        try {
            $command = new AddRecipeStepCommand();
            $command->slug = $this->slugGenerator->generateFrom($this->currentRecipeName);
            $command->type = RecipeActions::POUR_INTO_SHAKER;
            $command->name = $name;
            $command->capacity = (int) $capacity;

            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->lastException = $exception;
        }
    }
    
    /**
     * @When I add step: garnish glass with :name
     */
    public function iAddStepGarnishGlassWith($name)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = $this->slugGenerator->generateFrom($this->currentRecipeName);
        $command->type = RecipeActions::GARNISH_GLASS;
        $command->name = $name;

        $this->commandBus->handle($command);
    }

    /**
     * @When I remove first step from the recipe
     */
    public function iRemoveFirstStepFromTheRecipe()
    {
        try {
            $command = new RemoveRecipeStepCommand();
            $command->slug = $this->slugGenerator->generateFrom($this->currentRecipeName);
            $command->number = 1;

            $this->commandBus->handle($command);
        } catch (Exception $exception) {
            $this->lastException = $exception;
        }
    }
    
    /**
     * @Then :name recipe should be made from :stepsCount following steps
     */
    public function recipeShouldBeMadeFromFollowingSteps($stepsCount, $name, TableNode $stepsList)
    {
        $recipe = $this->recipes->findRecipeByName(new Name($name));
        expect(count($recipe->getSteps()))->toBe((int) $stepsCount);
        
        $steps = $recipe->getSteps();
        foreach ($stepsList->getHash() as $index => $stepData) {
            $step = $steps[$index];
            
            switch ($stepData['Type']) {
                case 'Prepare glass':
                    expect($step)->toBeAnInstanceOf(Step\PrepareTheGlass::class);
                    expect((string) $step->getName())->toBe($stepData['Name']);
                    expect($step->getCapacity()->getMilliliters())->toBe((int) $stepData['Capacity']);
                    break;
                case 'Pour':
                    expect($step)->toBeAnInstanceOf(Step\PourIntoGlass::class);
                    expect((string) $step->getName())->toBe($stepData['Name']);
                    expect($step->getCapacity()->getMilliliters())->toBe((int) $stepData['Capacity']);
                    break;
                case 'Add ingredient':
                    expect($step)->toBeAnInstanceOf(Step\AddIngredientIntoGlass::class);
                    expect((string) $step->getIngredientName())->toBe($stepData['Name']);
                    expect($step->getAmount()->getValue())->toBe((int) $stepData['Amount']);
                    break;
                case 'Garnish':
                    expect($step)->toBeAnInstanceOf(Step\GarnishGlass::class);
                    expect((string) $step->getDecorationName())->toBe($stepData['Name']);
                    break;
            }
        }
    }

    /**
     * @Then I should be noticed that glass from recipe can't took more than :amount ml of liquids
     */
    public function iShouldBeNoticedThatGlassFromRecipeCanTTookMoreThanMlOfLiquids($amount)
    {
        expect($this->lastException)->toNotBe(null);
        expect($this->lastException)->toBeAnInstanceOf(GlassCapacityOverflowException::class);
    }

    /**
     * @Then I should be noticed that first step should be glass preparation
     */
    public function iShouldBeNoticedThatFirstStepShouldBeGlassPreparation()
    {
        expect($this->lastException)->toNotBe(null);
        expect($this->lastException)->toBeAnInstanceOf(MissingGlassException::class);
    }

    /**
     * @Then I should be noticed that first step should be shaker preparation
     */
    public function iShouldBeNoticedThatFirstStepShouldBeShakerPreparation()
    {
        expect($this->lastException)->toNotBe(null);
        expect($this->lastException)->toBeAnInstanceOf(MissingShakerException::class);
    }

    /**
     * @Then the recipe should not be published
     */
    public function theRecipeShouldNotBePublished()
    {
        $recipe = $this->recipes->findRecipeByName($this->currentRecipeName);
        expect($recipe->isPublished())->toBe(false);
    }
}
