<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Validator\Constraints;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipes;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueRecipeNameValidator extends ConstraintValidator
{
    /**
     * @var Recipes
     */
    private $recipes;

    /**
     * @param Recipes $recipes
     */
    public function __construct(Recipes $recipes)
    {
        $this->recipes = $recipes;
    }
    
    public function validate($value, Constraint $constraint)
    {
        try {
            $name = new Name($value);
        } catch (InvalidArgumentException $e) {
            // Name could not be created
            return;
        }
        
        if ($this->recipes->hasRecipeWithName($name)) {
            $this->context->addViolation($constraint->message);
        }
    }
}