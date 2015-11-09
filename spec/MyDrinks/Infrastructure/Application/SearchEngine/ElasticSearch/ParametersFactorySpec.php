<?php

namespace spec\MyDrinks\Infrastructure\Application\SearchEngine\ElasticSearch;

use MyDrinks\Application\Recipe\Description\TasteBuilder;
use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\Application\SlugGenerator;
use MyDrinks\Domain\Name;
use MyDrinks\Infrastructure\UserInterface\ElasticSearch;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParametersFactorySpec extends ObjectBehavior
{
    function let(SlugGenerator $slugGenerator)
    {
        $this->beConstructedWith($slugGenerator);
    }
    
    function it_build_parameters_for_simple_query_seearch()
    {
        $criteria = new Criteria();
        $criteria->addQuery("drink");
        
        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'multi_match' => [
                        'query' => "drink",
                        "type" => "phrase_prefix",
                        'fields' => ["description.text^2", "name^4"],
                    ]
                ],
                'filter' => [
                    'exists' => [
                        'field' => 'publicationDate'
                    ]
                ]
            ]
        ]);
    }
    
    function it_build_parameters_without_only_published_filter()
    {
        $criteria = new Criteria(0, false);
        $criteria->addQuery("drink");

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'multi_match' => [
                        'query' => $criteria->getQuery(),
                        "type" => "phrase_prefix",
                        'fields' => ["description.text^2", "name^4"],
                    ]
                ]
            ],
        ]);
    }
        
    function it_build_parameters_with_sorting()
    {
        $criteria = new Criteria(0, false);
        $criteria->addQuery("drink");
        $criteria->sortBy('name');

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'multi_match' => [
                        'query' => $criteria->getQuery(),
                        "type" => "phrase_prefix",
                        'fields' => ["description.text^2", "name^4"],
                    ]
                ],
                'sort' => [
                    'name' => ['order' => Criteria::SORT_DESC]
                ]
            ],
        ]);
    }
    
    function it_build_parameters_for_required_ingredients()
    {
        $criteria = new Criteria(0, false);
        $criteria->mustContainIngredients(['vodka']);

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                [
                                    'terms' => [
                                        'steps.name' => ['vodka']
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }

    function it_build_parameters_for_optional_ingredients()
    {
        $criteria = new Criteria(0, false);
        $criteria->mayContainIngredients(['vodka', 'orange juice']);

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'or' => [
                                [
                                    'terms' => [
                                        'steps.name' => ['vodka']
                                    ]
                                ],
                                [
                                    'terms' => [
                                        'steps.name' => ['orange juice']
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }

    function it_build_parameters_for_optional_and_required_ingredients()
    {
        $criteria = new Criteria(0, false);
        $criteria->mayContainIngredients(['vodka']);
        $criteria->mustContainIngredients(['orange juice']);

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                [
                                    'terms' => [
                                        'steps.name' => ['orange juice']
                                    ]
                                ],
                            ],
                            'or' => [
                                [
                                    'terms' => [
                                        'steps.name' => ['vodka']
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }
    
    function it_build_parameters_for_required_tastes()
    {
        $criteria = new Criteria(0, false);
        $criteria->updateRequiredTaste((new TasteBuilder())->sweet()->sour()->buildTaste());

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                [
                                    'terms' => [
                                        'description.taste' => ['sweet', 'sour']
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }

    function it_build_parameters_for_optional_tastes()
    {
        $criteria = new Criteria(0, false);
        $criteria->updateOptionalTaste((new TasteBuilder())->sweet()->sour()->buildTaste());

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'or' => [
                                [
                                    'terms' => [
                                        'description.taste' => ['sweet']
                                    ]
                                ],
                                [
                                    'terms' => [
                                        'description.taste' => ['sour']
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }
    
    function it_build_parameters_with_glass_filter()
    {
        $criteria = new Criteria(0, false);
        $criteria->withGlass("glass.highball");

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                [
                                    'term' => [
                                        'glass' => 'glass.highball'
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }
    
    function it_build_parameters_for_ingredients_and_query()
    {
        $criteria = new Criteria(0, false);
        $criteria->addQuery('blue');
        $criteria->mustContainIngredients(['vodka']);

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                [
                                    'terms' => [
                                        'steps.name' => ['vodka']
                                    ]
                                ]
                            ]
                        ],
                        'query' => [
                            'multi_match' => [
                                'query' => 'blue',
                                "type" => "phrase_prefix",
                                'fields' => ["description.text^2", "name^4"],
                            ]
                        ]
                    ],
                ],
            ],
        ]);
    }
    
    function it_build_parameters_for_similar_recipes(SlugGenerator $slugGenerator)
    {
        $criteria = new Criteria(0, false);
        $criteria->similarTo(new Name("John Collins"));

        $slugGenerator->generateFrom("John Collins")->willReturn('john-collins');
        
        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'query' => [
                            'more_like_this' => [
                                'fields' => ["steps.name", "description.taste"],
                                'ids' => ["john-collins"],
                                'min_term_freq' => 1,
                                'max_query_terms' => 25
                            ]
                        ]
                    ],
                ],
            ],
        ]);
    }

    function it_build_parameters_with_alcohol_content_range()
    {
        $criteria = new Criteria(0, false);
        $criteria->withAlcoholContentLowerThan(100);
        $criteria->withAlcoholContentGreaterThan(0);

        $this->createParameters($criteria)->shouldReturn([
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                [
                                    'range' => [
                                        'description.alcoholContent' => [
                                            'gte' => 0,
                                            'lte' => 100
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ]);
    }
}
