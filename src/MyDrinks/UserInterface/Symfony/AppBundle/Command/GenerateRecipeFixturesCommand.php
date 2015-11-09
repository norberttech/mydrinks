<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Command;

use Faker\Factory;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Description;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class GenerateRecipeFixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mydrinks:recipe:fixtures:load')
            ->setDescription('Generate and load 1k Recipe fixtures')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Removing old fixtures.");
        
        $fs = new Filesystem();
        $fs->remove($this->getContainer()->getParameter("recipes_upload_target_dir"));
        
        $output->writeln("Old fixtures successfully removed.");
        
        $output->writeln("Generating new fixtures");
        
        $storage = $this->getContainer()->get('my_drinks.recipe.storage');
        for ($i = 1; $i <= 1000; $i++) {
            $recipe = $this->createRecipe("Screwdriver " . $i);
            $storage->save($recipe);
            $output->write('.');
            
            if ($i % 100 == 0) {
                $output->writeln("");
            }
        }

        $output->writeln("");
        $output->writeln("Successfully generated 1k fixtures.");
    }

    private function createRecipe($name)
    {
        $faker = Factory::create();
        
        $recipe = new Recipe(new Name($name));
        $description = new Description();
        $description->markAsIBAOfficial();
        $description->setText($faker->text(1000));
        $recipe->updateDescription($description);
        
        $recipe->prepareTheGlass(
            new Name("Highball"),
            new Recipe\Supply\Capacity(250)
        );
        $recipe->addIngredientIntoGlass(
            new Name("Ice Cubes"),
            new Recipe\Supply\Amount(5)
        );
        $recipe->pourIntoGlass(
            new Name("Vodka"),
            new Recipe\Supply\Capacity(50)
        );
        $recipe->pourIntoGlass(
            new Name("Orange Juice"),
            new Recipe\Supply\Capacity(100)
        );
        $recipe->garnishGlass(
            new Name("Orange Slice"),
            new Recipe\Supply\Amount(1)
        );

        return $recipe;
    }
}