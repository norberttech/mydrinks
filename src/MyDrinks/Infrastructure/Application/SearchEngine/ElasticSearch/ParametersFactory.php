<?php

namespace MyDrinks\Infrastructure\Application\SearchEngine\ElasticSearch;

use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\Application\SlugGenerator;
use MyDrinks\Domain\Recipe\Description\Taste;
use MyDrinks\Infrastructure\UserInterface\ElasticSearch;

class ParametersFactory
{
    private $parameters = [];
    
    /**
     * @var SlugGenerator
     */
    private $slugGenerator;

    /**
     * @param SlugGenerator $slugGenerator
     */
    public function __construct(SlugGenerator $slugGenerator)
    {
        $this->slugGenerator = $slugGenerator;
    }

    public function createParameters(Criteria $criteria)
    {
        $this->parameters = [
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'body' => [
                'from' => $criteria->startFrom(),
                'size' => $criteria->getSize()
            ]
        ];

        $this->addRequiredIngredientsQueryFilter($criteria);
        $this->addGlassNameFilter($criteria);
        $this->addTasteQueryFilter($criteria);
        $this->addAlcoholContentQueryFilters($criteria);
        $this->addOptionalIngredientsQueryFilter($criteria);
        $this->addQuery($criteria);
        $this->addSimilarRecipe($criteria);
        $this->addOnlyPublishedFilter($criteria);
        $this->addSortBy($criteria);

        return $this->parameters;
    }

    /**
     * @param Criteria $criteria
     */
    private function addRequiredIngredientsQueryFilter(Criteria $criteria)
    {
        if ($criteria->hasRequiredIngredients()) {
            $this->parameters['body']['query']['filtered']['filter']['and'][] = [
                'terms' => [
                    'steps.name' => $criteria->getRequiredIngredients()
                ]
            ];
        }
    }

    /**
     * @param Criteria $criteria
     */
    private function addSimilarRecipe(Criteria $criteria)
    {
        if ($criteria->lookForSimilarRecipes()) {
            $this->parameters['body']['query']['filtered']['query'] = [
                'more_like_this' => [
                    'fields' => ["steps.name", "description.taste"],
                    'ids' => [$this->slugGenerator->generateFrom((string) $criteria->getSimilarToRecipeName())],
                    'min_term_freq' => 1,
                    'max_query_terms' => 25
                ]
            ];
        }
    }
    
    /**
     * @param Criteria $criteria
     */
    private function addOptionalIngredientsQueryFilter(Criteria $criteria)
    {
        if ($criteria->hasOptionalIngredients()) {
            foreach ($criteria->getOptionalIngredients() as $ingredientName) {
                $this->parameters['body']['query']['filtered']['filter']['or'][] = [
                    'terms' => [
                        'steps.name' => [$ingredientName]
                    ]
                ];
            }
        }
    }
    
    /**
     * @param Criteria $criteria
     */
    private function addGlassNameFilter(Criteria $criteria)
    {
        if ($criteria->hasGlassName()) {
            $this->parameters['body']['query']['filtered']['filter']['and'][] = [
                'term' => [
                    'glass' => $criteria->getGlassName()
                ]
            ];
        }
    }
    
    /**
     * @param Criteria $criteria
     */
    private function addOnlyPublishedFilter(Criteria $criteria)
    {
        if ($criteria->onlyPublished()) {
            $this->parameters['body']['filter'] = [
                'exists' => [
                    'field' => 'publicationDate'
                ]
            ];
        }
    }

    /**
     * @param Criteria $criteria
     */
    private function addSortBy(Criteria $criteria)
    {
        if ($criteria->sortResults()) {
            $sortBy = [];
            foreach ($criteria->getSortBy() as $field => $order) {
                $sortBy[$field] = ['order' => $order];
            }

            $this->parameters['body']['sort'] = $sortBy;
        }
    }

    /**
     * @param Criteria $criteria
     */
    private function addAlcoholContentQueryFilters(Criteria $criteria)
    {
        $alcoholContentGreaterThan = $criteria->getAlcoholContentGreaterThan();
        $alcoholContentLowerThan = $criteria->getAlcoholContentLowerThan();

        if (!is_null($alcoholContentGreaterThan) || !is_null($alcoholContentLowerThan)) {

            $range = [];
            if (!is_null($alcoholContentGreaterThan)) {
                $range['gte'] = $alcoholContentGreaterThan;
            }
            if (!is_null($alcoholContentLowerThan)) {
                $range['lte'] = $alcoholContentLowerThan;
            }

            $this->parameters['body']['query']['filtered']['filter']['and'][] = [
                'range' => [
                    'description.alcoholContent' => $range
                ]
            ];
        }
    }

    /**
     * @param Criteria $criteria
     */
    private function addQuery(Criteria $criteria)
    {
        if ($criteria->hasQuery()) {
            if (isset($this->parameters['body']['query']['filtered'])) {
                $this->parameters['body']['query']['filtered']['query'] = [
                    'multi_match' => [
                        'query' => $criteria->getQuery(),
                        "type" => "phrase_prefix",
                        'fields' => ["description.text^2", "name^4"],
                    ]
                ];
            } else {
                $this->parameters['body']['query'] = [
                    'multi_match' => [
                        'query' => $criteria->getQuery(),
                        "type" => "phrase_prefix",
                        'fields' => ["description.text^2", "name^4"],
                    ]
                ];
            }
        }
    }

    /**
     * @param Criteria $criteria
     */
    private function addTasteQueryFilter(Criteria $criteria)
    {
        $requiredTaste = $criteria->getRequiredTaste();
        $optionalTaste = $criteria->getOptionalTaste();
        
        if ($requiredTaste->isDefined()) {
            $this->parameters['body']['query']['filtered']['filter']['and'][] = [
                'terms' => [
                    'description.taste' => $this->buildTasteArray($requiredTaste)
                ]
            ];
        }

        if ($optionalTaste->isDefined()) {
            foreach ($this->buildTasteArray($optionalTaste) as $taste) {
                $this->parameters['body']['query']['filtered']['filter']['or'][] = [
                    'terms' => [
                        'description.taste' => [$taste]
                    ]
                ];
            }
        }
    }

    /**
     * @param $requiredTaste
     * @return array
     */
    private function buildTasteArray(Taste $requiredTaste)
    {
        $tastes = [];
        if ($requiredTaste->isSweet()) {
            $tastes[] = Tastes::SWEET;
        }

        if ($requiredTaste->isBitter()) {
            $tastes[] = Tastes::BITTER;
        }

        if ($requiredTaste->isSour()) {
            $tastes[] = Tastes::SOUR;
        }

        if ($requiredTaste->isSpicy()) {
            $tastes[] = Tastes::SPICY;
        }

        if ($requiredTaste->isSalty()) {
            $tastes[] = Tastes::SALTY;
        }
        
        return $tastes;
    }
}
