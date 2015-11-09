<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Command;

use MyDrinks\Application\AutoComplete\Item\Supply;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ElasticSearchIndexSuppliesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mydrinks:es:index:supplies')
            ->setDescription('Index all supplies in ES search engine')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var \Symfony\Component\Translation\Loader\YamlFileLoader $loader */
        $loader = $this->getContainer()->get('translation.loader.yml');

        $translations = $loader->load(
            $this->getContainer()->getParameter('kernel.root_dir') . '/Resources/translations/supplies.pl.yml',
            'pl_PL'
        );

        $autoComplete = $this->getContainer()->get('my_drinks.autocomplete');
        
        $indexedCount = 0;
        foreach ($translations->all('messages') as $key => $translation) {
            $polishName = $this->getContainer()->get('translator')->transChoice($key, 0, [], 'supplies');
            $autoComplete->indexSupply(new Supply($key, $polishName));
            $indexedCount++;
        }
        
        $output->writeln(sprintf("Successful indexed \"%d\" supply liquids.", $indexedCount));
    }
}