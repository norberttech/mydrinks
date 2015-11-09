<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Command;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use MyDrinks\Infrastructure\UserInterface\ElasticSearch;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ElasticSearchCreateIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mydrinks:es:create:index')
            ->setDescription("This command should be executed before reindexing everything.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get('elasticsearch.client');

        try {
            $client->indices()->delete(['index' => ElasticSearch::INDEX]);
        } catch (Missing404Exception $e) {
        } // do nothing, index might be never created.

        $indexParams = [
            'index' => ElasticSearch::INDEX,
            'body' => [
                'mappings' => [
                    'supply' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'polishName' => [
                                'type' => 'string',
                                'analyzer' => 'ascii_folding'
                            ]
                        ]
                    ],
                    "recipe" => [
                        "properties" => [
                            "name" => [
                                "type" => "string",
                                "analyzer" => "ascii_folding",
                                "fields" => [
                                "raw" => [
                                    "type" => "string",
                                    "index" => "not_analyzed"
                                    ]
                                ]
                            ],
                            "description" => [
                                "properties" => [
                                    "IBAOfficial" => [
                                        "type" => "boolean"
                                    ],
                                    "text" => [
                                        "type" => "string"
                                    ]
                                ]
                            ],
                            "publicationDate" => [
                                "type" => "date",
                                "format" => "yyyy-MM-dd HH:mm:ss"
                            ],
                            "steps" => [
                                "properties" => [
                                    "amount" => [
                                        "type" => "long"
                                    ],
                                    "capacity" => [
                                        "type" => "long"
                                    ],
                                    "name" => [
                                        "type" => "string"
                                    ],
                                    "type" => [
                                        "type" => "string"
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'settings' => [
                    "analysis" => [
                        "analyzer" => [
                            "ascii_folding" => [
                                "tokenizer" => "standard",
                                "filter" => ["standard", "asciifolding", "lowercase"]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client->indices()->create($indexParams);
        $client->cluster()->health(['wait_for_status' => 'yellow']);
    }
}