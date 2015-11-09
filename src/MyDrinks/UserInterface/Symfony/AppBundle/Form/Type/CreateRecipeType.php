<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use MyDrinks\Application\Command\CreateNewRecipeCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
                'label' => 'recipe.form.create.name.label'
            ])
            ->add('submit', 'submit', [
                'label' => 'recipe.form.create.submit.label'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateNewRecipeCommand::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'create_recipe';
    }
}