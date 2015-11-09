<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class UniqueRecipeName extends Constraint
{
    public $message = 'error.recipe.name.not_unique';

    public function validatedBy()
    {
        return 'unique_recipe_name_validator';
    }
}