<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use MyDrinks\Application\Command\UploadRecipeImageCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RecipeImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', 'file', [
                'required' => false,
                'label' => 'recipe.form.upload_image.file.label'
            ])->add('submit', 'submit', [
                'label' => 'recipe.form.upload_image.submit.label'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UploadRecipeImageCommand::class
        ]);
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'recipe_image';
    }
}