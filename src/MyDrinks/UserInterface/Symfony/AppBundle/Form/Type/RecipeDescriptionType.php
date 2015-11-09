<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use MyDrinks\Application\Command\UpdateRecipeDescriptionCommand;
use MyDrinks\Application\Recipe\Description\Tastes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RecipeDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', [
                'required' => false,
                'label' => 'recipe.form.update_description.text.label'
            ])
            ->add('alcoholContent', 'integer', [
                'required' => false,
                'label' => 'recipe.form.update_description.alcoholContent.label'
            ])
            ->add('IBAOfficial', 'checkbox', [
                'required' => false,
                'label' => 'recipe.form.update_description.IBAOfficial.label'
            ])
            ->add('taste', 'choice', [
                'required' => false,
                'label' => 'recipe.form.update_description.taste.label',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    Tastes::SWEET => 'recipe.form.update_description.taste.sweet',
                    Tastes::BITTER => 'recipe.form.update_description.taste.bitter',
                    Tastes::SOUR => 'recipe.form.update_description.taste.sour',
                    Tastes::SPICY => 'recipe.form.update_description.taste.spicy',
                    Tastes::SALTY => 'recipe.form.update_description.taste.salty'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateRecipeDescriptionCommand::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recipe_description';
    }
}