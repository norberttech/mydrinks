<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Controller;

use MyDrinks\Application\AutoComplete\Item\Supply;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SupplyController extends Controller
{
    public function autocompleteAction(Request $request, $query)
    {
        $supplies = $this->get('my_drinks.autocomplete')->supply($query);
        
        if ($request->isXmlHttpRequest()) {
            $liquidsArray = array_map(function(Supply $supply) {
                return ['id' => $supply->getId(), 'name' => $supply->getPolishName(), 'type' => $supply->getType()];
            }, $supplies);
            return new JsonResponse($liquidsArray);
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }
    
    public function autocompleteIngredientAction(Request $request, $query)
    {
        $supplies = $this->get('my_drinks.autocomplete')->ingredient($query);

        if ($request->isXmlHttpRequest()) {
            $liquidsArray = array_map(function(Supply $supply) {
                return ['id' => $supply->getId(), 'name' => $supply->getPolishName(), 'type' => $supply->getType()];
            }, $supplies);
            return new JsonResponse($liquidsArray);
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }
}