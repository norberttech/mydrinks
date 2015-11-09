<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Command;

use MyDrinks\Application\AutoComplete\Item\Supply;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ElasticSearchIndexRecipesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mydrinks:es:index:recipes')
            ->setDescription('Index all recipes in ES search engine')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var \Symfony\Component\Translation\Loader\YamlFileLoader $loader */
        $loader = $this->getContainer()->get('translation.loader.yml');

        $searchEngine = $this->getContainer()->get('my_drinks.search_engine');
        
        $storage = $this->getContainer()->get('my_drinks.recipe.storage');
        
        $indexedCount = 0;
        foreach ($storage->fetchAll() as $recipe) {
            $searchEngine->indexRecipe($recipe);
            $indexedCount++;
        }

        $output->writeln(sprintf("Successful indexed \"%d\" recipes.", $indexedCount));
    }
}