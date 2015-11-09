<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Validator;

use MyDrinks\Application\Command\AddRecipeStepCommand;
use MyDrinks\Application\Recipe\Actions;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RecipeStepValidator
{
    public static function validateRequiredField($object, ExecutionContextInterface $context)
    {
        if (!$object instanceof AddRecipeStepCommand) {
            return ;
        }

        switch ($object->type) {
            case Actions::PREPARE_GLASS:
                self::validateCapacity($object, $context);
                self::validateAmount($object, $context);
                break;
            case Actions::POUR_INTO_GLASS:
            case Actions::PREPARE_SHAKER:
                self::validateCapacity($object, $context);
                break;
            case Actions::ADD_INGREDIENT_INTO_GLASS:
            case Actions::ADD_INGREDIENT_INTO_SHAKER:
                self::validateAmount($object, $context);
                break;
        }
    }

    /**
     * @param $command
     * @param ExecutionContextInterface $context
     */
    private static function validateAmount(AddRecipeStepCommand $command, ExecutionContextInterface $context)
    {
        if (!isset($command->amount)) {
            $context->buildViolation('This value should not be blank.')
                ->atPath('amount')
                ->addViolation();
        }
    }

    /**
     * @param $command
     * @param ExecutionContextInterface $context
     */
    private static function validateCapacity(AddRecipeStepCommand $command, ExecutionContextInterface $context)
    {
        if (!isset($command->capacity)) {
            $context->buildViolation('This value should not be blank.')
                ->atPath('capacity')
                ->addViolation();
        }
    }
}