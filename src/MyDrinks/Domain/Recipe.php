<?php

namespace MyDrinks\Domain;

use MyDrinks\Domain\Exception\Recipe\MissingGlassException;
use MyDrinks\Domain\Exception\Recipe\MissingShakerException;
use MyDrinks\Domain\Exception\UnknownStepException;
use MyDrinks\Domain\Recipe\Description;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Steps;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\BarAccessory\Glass;
use MyDrinks\Domain\Recipe\BarAccessory\Shaker;

class Recipe
{
    /**
     * @var Name
     */
    private $name;

    /**
     * @var Steps
     */
    private $steps;

    /**
     * @var \DateTimeImmutable|null
     */
    private $publicationDate;

    /**
     * @var Description
     */
    private $description;

    /**
     * @var Glass|null
     */
    private $glass;

    /**
     * @var Shaker|null
     */
    private $shaker;

    /**
     * @param Name $name
     */
    public function __construct(Name $name)
    {
        $this->name = $name;
        $this->steps = [];
        $this->publicationDate = null;
        $this->description = new Description();
        $this->steps = new Steps();
    }

    /**
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Description $description
     */
    public function updateDescription(Description $description)
    {
        $this->description = $description;
    }

    public function publish()
    {
        $this->publicationDate = new \DateTimeImmutable();
    }
    
    /**
     * @return bool
     */
    public function isPublished()
    {
        return !is_null($this->publicationDate);
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Steps
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param $number
     * @throws UnknownStepException
     */
    public function removeStep($number)
    {
        $index = (int) $number - 1;
        unset($this->steps[$index]);
        $this->reorderSteps();
    }
    
    /**
     * @param Name $glassName
     * @param Supply\Capacity $capacity
     * @param Supply\Amount|null $amount
     */
    public function prepareTheGlass(Name $glassName, Supply\Capacity $capacity, Supply\Amount $amount = null)
    {
        $this->glass = new Glass($glassName, $capacity, $amount);
        $this->steps[] = new Step\PrepareTheGlass(
            $this->glass->getName(), 
            $this->glass->getCapacity(), 
            $this->glass->getAmount()
        );
    }

    /**
     * @return bool
     */
    public function isGlassRequired()
    {
        return !is_null($this->glass);
    }

    /**
     * @return Glass|null
     * @throws MissingGlassException
     */
    public function getGlass()
    {
        if (!$this->isGlassRequired()) {
            throw new MissingGlassException();
        }

        return $this->glass;
    }

    /**
     * @param Supply\Capacity $capacity
     */
    public function prepareTheShaker(Supply\Capacity $capacity)
    {
        $this->shaker = new Shaker($capacity);
        
        $this->steps[] = new Step\PrepareTheShaker($capacity);
    }

    /**
     * @return bool
     */
    public function isShakerRequired()
    {
        return !is_null($this->shaker);
    }

    /**
     * @return Shaker|null
     * @throws MissingShakerException
     */
    public function getShaker()
    {
        if (!$this->isShakerRequired()) {
            throw new MissingShakerException();
        }

        return $this->shaker;
    }

    /**
     * @param Name $liquid
     * @param Supply\Capacity $capacity
     * @throws Exception\Recipe\GlassCapacityOverflowException
     * @throws MissingGlassException
     */
    public function pourIntoGlass(Name $liquid, Supply\Capacity $capacity)
    {
        $this->getGlass()->pourIn($capacity);

        $this->steps[] = new Step\PourIntoGlass($liquid, $capacity);
    }

    /**
     * @param Name $liquid
     * @param Supply\Capacity $capacity
     * @throws Exception\Recipe\ShakerCapacityOverflowException
     * @throws MissingShakerException
     */
    public function pourIntoShaker(Name $liquid, Supply\Capacity $capacity)
    {
        $this->getShaker()->pourIn($capacity);

        $this->steps[] = new Step\PourIntoShaker($liquid, $capacity);
    }

    public function shakeShakerContent()
    {
        $this->getShaker()->shake();
        $this->steps[] = new Step\ShakeShakerContent();
    }
    
    /**
     * @throws \MyDrinks\Domain\Exception\Recipe\LiquidsNotShakedException
     * @throws \MyDrinks\Domain\Exception\Recipe\NotEnoughLiquidException
     * @throws Exception\Recipe\GlassCapacityOverflowException
     * @throws MissingGlassException
     * @throws MissingShakerException
     */
    public function strainIntoGlassFromShaker()
    {
        $shakedLiquid = $this->getShaker()->pourOut($this->getShaker()->getCurrentCapacity());
        $this->getGlass()->pourIn($shakedLiquid);
        $this->steps[] = new Step\StrainIntoGlassFromShaker();
    }

    /**
     * @param Name $content
     * @throws \MyDrinks\Domain\Exception\Recipe\AlreadyFilledException
     * @throws MissingGlassException
     */
    public function fillGlassWith(Name $content)
    {
        $this->getGlass()->fillWith($content);

        $this->steps[] = new Step\FillGlass($content);
    }

    /**
     * @param Name $content
     * @throws \MyDrinks\Domain\Exception\Recipe\AlreadyFilledException
     * @throws MissingShakerException
     */
    public function fillShakerWith(Name $content)
    {
        $this->getShaker()->fillWith($content);

        $this->steps[] = new Step\FillShaker($content);
    }

    /**
     * @throws \MyDrinks\Domain\Exception\Recipe\EmptyVesselException
     * @throws MissingGlassException
     */
    public function emptyTheGlass()
    {
        $this->getGlass()->emptyTheContent();

        $this->steps[] = new Step\EmptyTheGlass();
    }

    /**
     * @throws \MyDrinks\Domain\Exception\Recipe\EmptyVesselException
     * @throws MissingGlassException
     */
    public function stirGlassContent()
    {
        $this->getGlass()->stir();

        $this->steps[] = new Step\StirGlassContent();
    }

    /**
     * @throws \MyDrinks\Domain\Exception\Recipe\EmptyVesselException
     * @throws MissingGlassException
     */
    public function igniteGlassContent()
    {
        $this->getGlass()->ignite();
        
        $this->steps[] = new Step\IgniteGlassContent();
    }

    /**
     * @param Name $name
     * @throws Exception\Recipe\GlassCapacityOverflowException
     */
    public function topUpGlass(Name $name)
    {
        $capacity = $this->glass->topUp();
        
        $this->steps[] = new Step\TopUpGlass($name, $capacity);
    }

    /**
     * @param Name $ingredientName
     * @param Amount $amount
     * @throws MissingGlassException
     */
    public function addIngredientIntoGlass(Name $ingredientName, Amount $amount)
    {
        $this->getGlass()->addIngredient($ingredientName, $amount);
        
        $this->steps[] = new Step\AddIngredientIntoGlass($ingredientName, $amount);
    }

    /**
     * @param Name $ingredientName
     * @param Amount $amount
     * @throws MissingShakerException
     */
    public function addIngredientIntoShaker(Name $ingredientName, Amount $amount)
    {
        $this->getShaker()->addIngredient($ingredientName, $amount);
        
        $this->steps[] = new Step\AddIngredientIntoShaker($ingredientName, $amount);
    }

    /**
     * @param Name $decorationName
     * @throws MissingGlassException
     */
    public function garnishGlass(Name $decorationName)
    {
        $this->getGlass()->garnish($decorationName);

        $this->steps[] = new Step\GarnishGlass($decorationName);
    }

    /**
     * @throws Exception\Recipe\ContentAlreadyMuddledException
     * @throws Exception\Recipe\EmptyVesselException
     * @throws MissingGlassException
     */
    public function muddleContent()
    {
        $this->getGlass()->muddle();

        $this->steps[] = new Step\MuddleGlassContent();
    }

    /**
     * @return boolean
     */
    public function isMuddlerRequired()
    {
        foreach ($this->steps as $step) {
            if ($step instanceof Step\MuddleGlassContent) {
                return true;
            }
        }
        
        return false;
    }
    
    private function reorderSteps()
    {
        $steps = clone $this->steps;
        $this->steps->clear();
        $this->glass = null;
        $this->shaker = null;
        
        foreach ($steps as $step) {
            switch (get_class($step)) {
                case Step\AddIngredientIntoGlass::class:
                    $this->addIngredientIntoGlass($step->getIngredientName(), $step->getAmount());
                    break;
                case Step\AddIngredientIntoShaker::class:
                    $this->addIngredientIntoGlass($step->getIngredientName(), $step->getAmount());
                    break;
                case Step\EmptyTheGlass::class:
                    $this->emptyTheGlass();
                    break;
                case Step\FillGlass::class:
                    $this->fillGlassWith($step->getContentName());
                    break;
                case Step\FillShaker::class:
                    $this->fillShakerWith($step->getContentName());
                    break;
                case Step\GarnishGlass::class:
                    $this->garnishGlass($step->getDecorationName());
                    break;
                case Step\IgniteGlassContent::class:
                    $this->igniteGlassContent();
                    break;
                case Step\MuddleGlassContent::class:
                    $this->muddleContent();
                    break;
                case Step\PourIntoGlass::class:
                    $this->pourIntoGlass($step->getName(), $step->getCapacity());
                    break;
                case Step\PourIntoShaker::class:
                    $this->pourIntoShaker($step->getName(), $step->getCapacity());
                    break;
                case Step\PrepareTheGlass::class:
                    $this->prepareTheGlass($step->getName(), $step->getCapacity(), $step->getAmount());
                    break;
                case Step\PrepareTheShaker::class:
                    $this->prepareTheShaker($step->getCapacity());
                    break;
                case Step\ShakeShakerContent::class:
                    $this->shakeShakerContent();
                    break;
                case Step\StirGlassContent::class:
                    $this->stirGlassContent();
                    break;
                case Step\StrainIntoGlassFromShaker::class:
                    $this->strainIntoGlassFromShaker();
                    break;
                case Step\TopUpGlass::class:
                    $this->topUpGlass($step->getName());
                    break;
            }
        }
    }
}
