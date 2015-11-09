<?php

namespace MyDrinks\Infrastructure\Application\SearchEngine;

use Elasticsearch\Client;
use MyDrinks\Application\SearchEngine;
use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\Application\SearchEngine\SearchResultSlice;
use MyDrinks\Application\SlugGenerator;
use MyDrinks\Domain\Recipe;
use MyDrinks\Infrastructure\Application\SearchEngine\ElasticSearch\ParametersFactory;
use MyDrinks\Infrastructure\UserInterface\ElasticSearch;
use MyDrinks\Infrastructure\UserInterface\RecipeConverter as RecipeConverter;

final class ElasticSearchAdapter implements SearchEngine
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var SlugGenerator
     */
    private $slugGenerator;

    /**
     * @var RecipeConverter
     */
    private $recipeConverter;

    /**
     * @var ParametersFactory
     */
    private $parametersBuiler;

    /**
     * @param Client $client
     * @param SlugGenerator $slugGenerator
     */
    public function __construct(Client $client, SlugGenerator $slugGenerator)
    {
        $this->client = $client;
        $this->slugGenerator = $slugGenerator;
        $this->recipeConverter = new RecipeConverter();
        $this->parametersBuiler = new ParametersFactory($this->slugGenerator);
    }
    
    /**
     * @param Recipe $recipe
     */
    public function indexRecipe(Recipe $recipe)
    {
        $params = [
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'id' => $this->slugGenerator->generateFrom((string) $recipe->getName()),
            'body' => $this->recipeConverter->toArray($recipe)
        ];

        $this->client->index($params);
        $this->client->indices()->refresh();
    }

    /**
     * @param Recipe $recipe
     */
    public function removeRecipeFromIndex(Recipe $recipe)
    {
        $params = [
            'index' => ElasticSearch::INDEX,
            'type' => 'recipe',
            'id' => $this->slugGenerator->generateFrom((string) $recipe->getName()),
        ];
        
        $this->client->delete($params);
        $this->client->indices()->refresh();
    }

    /**
     * @param Criteria $criteria
     * @return SearchResultSlice
     */
    public function search(Criteria $criteria)
    {
        $results = $this->client->search($this->parametersBuiler->createParameters($criteria));

        if ($results['hits']['total'] === 0) {
            return new SearchResultSlice($criteria, [], 0);
        }
        
        $recipes = [];
        foreach ($results['hits']['hits'] as $result) {
            $recipe = new SearchEngine\Result\Recipe(
                $result['_id'],
                $result['_source']['name'],
                $result['_source']['description']['text']
            );
            
         
            if (!is_null($result['_source']['publicationDate'])) {
                $recipe->publishedAt(new \DateTimeImmutable($result['_source']['publicationDate']));
            }

            $recipes[] = $recipe;
        }

        return new SearchResultSlice($criteria, $recipes, $results['hits']['total']);
    }
}