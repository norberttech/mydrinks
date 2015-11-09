<?php

namespace MyDrinks\Infrastructure\Application\AutoComplete;

use Elasticsearch\Client;
use MyDrinks\Application\AutoComplete;
use MyDrinks\Application\AutoComplete\Item\Supply;
use MyDrinks\Infrastructure\UserInterface\ElasticSearch;

final class ElasticSearchAdapter implements AutoComplete
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Supply $supply
     */
    public function indexSupply(Supply $supply)
    {
        $params = [
            'index' => ElasticSearch::INDEX,
            'type' => 'supply',
            'id' => $supply->getId(),
            'body' => [
                'polishName' => $supply->getPolishName()
            ]
        ];

        $this->client->index($params);
        $this->client->indices()->refresh();
    }

    /**
     * @param string $namePartial
     * @param int $limit
     * @return array
     */
    public function supply($namePartial, $limit = 10)
    {
        $params = [
            'index' => ElasticSearch::INDEX,
            'type' => 'supply',
            'body' => [
                'query' => [
                    'match_phrase_prefix' => [
                        'polishName' => [
                            'query' => $namePartial,
                            'max_expansions' => 10
                        ]
                    ]
                ]
            ]
        ];
        
        $results = $this->client->search($params);
        
        if ($results['hits']['total'] === 0) {
            return [];
        }
        
        $liquids = [];
        foreach ($results['hits']['hits'] as $result) {
            $liquids[] = new Supply($result['_id'], $result['_source']['polishName']);
        }
        
        return $liquids;
    }

    /**
     * @param string $ingredientNamePartial
     * @param int $limit
     * @return array []
     */
    public function ingredient($ingredientNamePartial, $limit = 10)
    {
        $params = [
            'index' => ElasticSearch::INDEX,
            'type' => 'supply',
            'body' => [
                'query' => [
                    'match_phrase_prefix' => [
                        'polishName' => [
                            'query' => $ingredientNamePartial,
                            'max_expansions' => 10
                        ]
                    ]
                ],
                'filter' => [
                    'or' => [
                        [
                            'prefix' => [
                                '_id' => 'ingredient.'
                            ]
                        ],
                        [
                            'prefix' => [
                                '_id' => 'liquid.'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $results = $this->client->search($params);

        if ($results['hits']['total'] === 0) {
            return [];
        }

        $liquids = [];
        foreach ($results['hits']['hits'] as $result) {
            $liquids[] = new Supply($result['_id'], $result['_source']['polishName']);
        }

        return $liquids;
    }

    /**
     * @param $glassNamePartial
     * @param int $limit
     * @return array []
     */
    public function glass($glassNamePartial, $limit = 10)
    {
        $params = [
            'index' => ElasticSearch::INDEX,
            'type' => 'supply',
            'body' => [
                'query' => [
                    'match_phrase_prefix' => [
                        'polishName' => [
                            'query' => $glassNamePartial,
                            'max_expansions' => 10
                        ]
                    ]
                ],
                'filter' => [
                    'prefix' => [
                        '_id' => 'glass.'
                    ]
                ]
            ]
        ];

        $results = $this->client->search($params);

        if ($results['hits']['total'] === 0) {
            return [];
        }

        $liquids = [];
        foreach ($results['hits']['hits'] as $result) {
            $liquids[] = new Supply($result['_id'], $result['_source']['polishName']);
        }

        return $liquids;
    }
}