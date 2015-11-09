<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\SearchEngine;

use MyDrinks\Application\Recipe\Description\TasteBuilder;
use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\Application\SearchEngine\CriteriaDecorator;
use Symfony\Component\Form\FormInterface;

final class SearchCriteriaDecorator implements CriteriaDecorator
{
    /**
     * @var FormInterface
     */
    private $form;

    public function __construct(FormInterface $form)
    {
        $this->validateForm($form);
        $this->form = $form;
    }
    
    /**
     * @param Criteria $criteria
     */
    public function decorate(Criteria $criteria)
    {
        $alcoholFrom = $this->form->get('alcohol')->get('from')->getData();
        $alcoholTo = $this->form->get('alcohol')->get('to')->getData();
        
        if (!is_null($alcoholFrom)) {
            $criteria->withAlcoholContentGreaterThan($alcoholFrom);
        }

        if (!is_null($alcoholTo)) {
            $criteria->withAlcoholContentLowerThan($alcoholTo);
        }

        $criteria->addQuery($this->form->get('query')->getData());
        $criteria->mustContainIngredients($this->form->get('ingredients')->getData());


        $tasteBuilder = new TasteBuilder();
        foreach ($this->form->get('taste')->getData() as $tasteName) {
            switch ($tasteName) {
                case Tastes::SWEET:
                    $tasteBuilder->sweet();
                    break;
                case Tastes::BITTER:
                    $tasteBuilder->bitter();
                    break;
                case Tastes::SALTY:
                    $tasteBuilder->salty();
                    break;
                case Tastes::SPICY:
                    $tasteBuilder->spicy();
                    break;
                case Tastes::SOUR:
                    $tasteBuilder->sour();
                    break;
            }
        }
        
        $criteria->updateRequiredTaste($tasteBuilder->buildTaste());
    }

    /**
     * @param FormInterface $form
     */
    private function validateForm(FormInterface $form)
    {
        if (!$form->isSubmitted()) {
            throw new \InvalidArgumentException("FormCriteriaDecorator expects form to be submitted");
        }
        
        if (!$form->isValid()) {
            throw new \InvalidArgumentException("FormCriteriaDecorator expects form to be valid.");
        }
    }
}