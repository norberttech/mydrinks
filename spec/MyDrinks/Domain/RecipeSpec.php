<?php

namespace spec\MyDrinks\Domain;

use MyDrinks\Domain\Exception\Recipe\MissingShakerException;
use MyDrinks\Domain\Exception\Recipe\MissingGlassException;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step\AddIngredientIntoGlass;
use MyDrinks\Domain\Recipe\Step\AddIngredientIntoShaker;
use MyDrinks\Domain\Recipe\Step\EmptyTheGlass;
use MyDrinks\Domain\Recipe\Step\FillGlass;
use MyDrinks\Domain\Recipe\Step\FillShaker;
use MyDrinks\Domain\Recipe\Step\GarnishGlass;
use MyDrinks\Domain\Recipe\Step\IgniteGlassContent;
use MyDrinks\Domain\Recipe\Step\MuddleGlassContent;
use MyDrinks\Domain\Recipe\Step\PourIntoGlass;
use MyDrinks\Domain\Recipe\Step\StrainIntoGlassFromShaker;
use MyDrinks\Domain\Recipe\Step\PrepareTheGlass;
use MyDrinks\Domain\Recipe\Step\ShakeShakerContent;
use MyDrinks\Domain\Recipe\Step\StirGlassContent;
use MyDrinks\Domain\Recipe\Step\TopUpGlass;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use MyDrinks\Domain\Recipe\Step\PourIntoShaker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Name("Screwdriver"));
    }
    
    function it_has_a_name()
    {
        $this->getName()->__toString()->shouldReturn("Screwdriver");
    }
    
    function it_has_empty_description()
    {
        $this->getDescription()->isOfficialIBA()->shouldReturn(false);
        $this->getDescription()->getText()->shouldReturn(null);
    }
    
    function its_not_published_by_default()
    {
        $this->isPublished()->shouldReturn(false);
    }
    
    function it_can_be_published()
    {
        $this->publish();
        $this->isPublished()->shouldReturn(true);
        $this->getPublicationDate()->shouldReturnAnInstanceOf(\DateTimeImmutable::class);
    }
    
    function it_throws_exception_when_accessing_glass_when_its_not_required()
    {
        $this->shouldthrow(MissingGlassException::class)->during("getGlass");
    }
    
    function it_has_glass_required_after_its_prepared()
    {
        $this->prepareTheGlass(new Name("vodka glass"), new Capacity(50), new Amount(2));
        
        $this->isGlassRequired()->shouldReturn(true);
        $this->getGlass()->getName()->__toString()->shouldReturn("vodka glass");
        $this->getGlass()->getTotalCapacity()->getMilliliters()->shouldReturn(100);
        $this->getSteps()[0]->shouldReturnAnInstanceOf(PrepareTheGlass::class);
    }

    function it_throws_exception_when_accessing_shaker_when_its_not_required()
    {
        $this->shouldthrow(MissingShakerException::class)->during("getShaker");
    }
    
    function it_has_shaker_required_after_its_prepared()
    {
        $this->prepareTheShaker(new Capacity(350));

        $this->isShakerRequired()->shouldReturn(true);
        $this->getShaker()->getCapacity()->getMilliliters()->shouldReturn(350);
    }
    
    function it_accept_pouring_into_glass()
    {
        $this->prepareTheGlass(new Name("vodka glass"), new Capacity(40), new Amount(6));
        $this->pourIntoGlass(new Name("vodka"), new Capacity(220));
        
        $this->getSteps()[1]->shouldReturnAnInstanceOf(PourIntoGlass::class);
    }

    function it_accept_pouring_into_shaker()
    {
        $this->prepareTheShaker(new Capacity(350));
        $this->pourIntoShaker(new Name("vodka"), new Capacity(220));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(PourIntoShaker::class);
    }

    function it_accept_pouring_from_shaker_into_glass()
    {
        $this->prepareTheShaker(new Capacity(350));
        $this->pourIntoShaker(new Name("vodka"), new Capacity(50));
        $this->pourIntoShaker(new Name("orange juice"), new Capacity(100));
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->shakeShakerContent();
        
        $this->strainIntoGlassFromShaker();

        $this->getGlass()->getCurrentCapacity()->getMilliliters()->shouldReturn(150);
        $this->getSteps()[4]->shouldReturnAnInstanceOf(ShakeShakerContent::class);
        $this->getSteps()[5]->shouldReturnAnInstanceOf(StrainIntoGlassFromShaker::class);
    }
    
    function it_accept_filling_glass()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->fillGlassWith(new Name("ice"));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(FillGlass::class);
    }

    function it_accept_filling_shaker()
    {
        $this->prepareTheShaker(new Capacity(250));
        $this->fillShakerWith(new Name("ice"));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(FillShaker::class);
    }
    
    function it_accept_emptying_the_glass()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->fillGlassWith(new Name("ice"));
        $this->emptyTheGlass();

        $this->getSteps()[2]->shouldReturnAnInstanceOf(EmptyTheGlass::class);
        $this->getGlass()->isFilled()->shouldReturn(false);
    }

    function it_accept_toping_up_the_glass()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->topUpGlass(new Name("vodka"));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(TopUpGlass::class);
        $this->getGlass()->isFull()->shouldReturn(true);
    }
    
    function it_accept_stirring_the_glass_content()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->pourIntoGlass(new Name("vodka"), new Capacity(150));
        $this->stirGlassContent();

        $this->getSteps()[2]->shouldReturnAnInstanceOf(StirGlassContent::class);
        $this->getGlass()->isStirred()->shouldReturn(true);
    }

    function it_accept_ignite_the_glass_content()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->pourIntoGlass(new Name("vodka"), new Capacity(150));
        $this->igniteGlassContent();

        $this->getSteps()[2]->shouldReturnAnInstanceOf(IgniteGlassContent::class);
        $this->getGlass()->isOnFire()->shouldReturn(true);
    }
    
    function it_accept_adding_ingredients_into_glass()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->addIngredientIntoGlass(new Name("orange slice"), new Amount(1));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(AddIngredientIntoGlass::class);
        $this->getGlass()->isEmpty()->shouldReturn(false);
    }

    function it_accept_adding_ingredients_into_shaker()
    {
        $this->prepareTheShaker(new Capacity(250));
        $this->addIngredientIntoShaker(new Name("orange slice"), new Amount(1));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(AddIngredientIntoShaker::class);
        $this->getShaker()->isEmpty()->shouldReturn(false);
    }
    
    function it_accept_decorating_glass()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->garnishGlass(new Name("orange slice"));

        $this->getSteps()[1]->shouldReturnAnInstanceOf(GarnishGlass::class);
        $this->getGlass()->isDecorated()->shouldReturn(true);
    }

    function it_accept_muddling_glass_content()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->addIngredientIntoGlass(new Name("orange slice"), new Amount(1));
        $this->muddleContent();

        $this->getSteps()[2]->shouldReturnAnInstanceOf(MuddleGlassContent::class);
        $this->isMuddlerRequired()->shouldReturn(true);
    }

    function it_allows_to_remove_the_step()
    {
        $this->prepareTheGlass(new Name("highball"), new Capacity(250));
        $this->removeStep(1);
        $this->getSteps()->shouldHaveCount(0);
    }
}
