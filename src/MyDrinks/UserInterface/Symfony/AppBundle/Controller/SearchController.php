<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Controller;

use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\UserInterface\Symfony\AppBundle\Form\Type\SearchType;
use MyDrinks\UserInterface\Symfony\AppBundle\SearchEngine\SearchCriteriaDecorator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    const PARAM_START = 'start';
    
    public function indexAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', new SearchType());
        $form->handleRequest($request);

        $criteria = new Criteria((int) $request->query->get(self::PARAM_START, 0));
        $criteria->changeSize(20);
        
        if ($form->isValid()) {
            $criteria->decorate(new SearchCriteriaDecorator($form));
        } else {
            if ($form->isSubmitted()) {
                $ingredients = $form->get('ingredients')->getData();
                $criteria->mustContainIngredients(is_null($ingredients) ? [] : $ingredients);
            }
        }
        
        $drinks = $this->get('my_drinks.search_engine')->search($criteria);
        
        return $this->render('search/index.html.twig', [
            'results' => null,
            'drinks' => $drinks
        ]);
    }
    
    public function searchAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', new SearchType());
        $form->handleRequest($request);
        
        return $this->render(':search/widget:search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}