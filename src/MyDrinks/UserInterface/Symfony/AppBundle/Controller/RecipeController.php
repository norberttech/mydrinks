<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Controller;

use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\Infrastructure\Application\SearchEngine\SimilarRecipeCriteriaFactory;
use MyDrinks\UserInterface\Symfony\AppBundle\Drink\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

final class RecipeController extends Controller
{
    public function displayAction($slug)
    {
        $recipe = $this->get('my_drinks.recipes')->findBySlug($slug);
        if (is_null($recipe)) {
            throw new NotFoundHttpException;
        }

        return $this->render('recipe/display.html.twig', [
            'recipe' => $recipe,
            'slug' => $slug
        ]);
    }

    public function similarRecipesAction($slug, $size)
    {
        $recipe = $this->get('my_drinks.recipes')->findBySlug($slug);
        if (is_null($recipe)) {
            throw new NotFoundHttpException;
        }
        
        $criteria = new Criteria();
        $criteria->similarTo($recipe->getName());
        $criteria->changeSize((int) $size);
        $results = $this->get('my_drinks.search_engine')->search($criteria);
        
        return $this->render(':recipe:similarRecipes.html.twig', [
            'recipe' => $recipe,
            'results' => $results
        ]);
    }
    
    /**
     * @ParamConverter("category")
     */
    public function categoryAction(Request $request, Category $category)
    {
        return $this->render(':recipe:category.html.twig', [
            'recipes' => $category->getDrinks((int) $request->query->get(SearchController::PARAM_START, 0)),
            'category' => $category
        ]);
    }
}