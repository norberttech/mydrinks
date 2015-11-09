<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use MyDrinks\Application\Command\AddRecipeStepCommand;
use MyDrinks\Application\Recipe\Actions;
use MyDrinks\Domain\Recipe\Step\PerformAnAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'choice', [
                'label' => 'recipe.form.add_step.type.label',
                'choices' => array(
                    Actions::PREPARE_GLASS   => 'recipe.form.add_step.type.prepareGlass',
                    Actions::POUR_INTO_GLASS => 'recipe.form.add_step.type.pourIntoGlass',
                    Actions::STRAIN_INTO_GLASS_FROM_SHAKER => 'recipe.form.add_step.type.strainIntoGlassFromShaker',
                    Actions::ADD_INGREDIENT_INTO_GLASS => 'recipe.form.add_step.type.addIngredientIntoGlass',
                    Actions::STIR_GLASS_CONTENT => 'recipe.form.add_step.type.stirGlassContent',
                    Actions::FILL_GLASS => 'recipe.form.add_step.type.fillGlassContent',
                    Actions::IGNITE_GLASS_CONTENT => 'recipe.form.add_step.type.igniteGlassContent',
                    Actions::GARNISH_GLASS => 'recipe.form.add_step.type.garnishGlass',
                    Actions::EMPTY_GLASS_CONTENT => 'recipe.form.add_step.type.emptyGlassContent',
                    Actions::MUDDLE_GLASS_CONTENT => 'recipe.form.add_step.type.muddleGlassContent',
                    Actions::TOP_UP_GLASS => 'recipe.form.add_step.type.topUpGlass',
                    
                    Actions::PREPARE_SHAKER  => 'recipe.form.add_step.type.prepareShaker',
                    Actions::POUR_INTO_SHAKER => 'recipe.form.add_step.type.pourIntoShaker',
                    Actions::SHAKE_SHAKER_CONTENT => 'recipe.form.add_step.type.shakeShakerContent',
                    Actions::FILL_SHAKER => 'recipe.form.add_step.type.fillShaker',
                    Actions::ADD_INGREDIENT_INTO_SHAKER => 'recipe.form.add_step.type.addIngredientIntoShaker'
                ),
            ])
            ->add('name', 'text', ['label' => 'recipe.form.add_step.name.label'])
            ->add('capacity', 'integer', ['required' => false, 'label' => 'recipe.form.add_step.capacity.label'])
            ->add('amount', 'integer', ['required' => false, 'label' => 'recipe.form.add_step.amount.label'])
            ->add('submit', 'submit', [
                'label' => 'recipe.form.add_step.submit.label'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddRecipeStepCommand::class
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'recipe_step';
    }
}