<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use MyDrinks\Application\Recipe\Description\Tastes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class SearchType extends AbstractType
{
    const NAME = 'search';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('query', 'search', [
                'required' => false,
                'label' => false,
                'attr' => [ 
                    'placeholder' => 'search.form.query.placeholder'
                ],
            ])
            ->add('ingredients', 'collection', [
                'type' => 'hidden',
                'allow_add' => true,
                'delete_empty' => true
            ])
            ->add('alcohol', new AlcoholContentType(), [
                'label' => 'search.form.alcohol.label'
            ])
            ->add('taste', 'choice', [
                'required' => false,
                'label' => 'search.form.taste.label',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    Tastes::SWEET => 'recipe.taste.sweet',
                    Tastes::BITTER => 'recipe.taste.bitter',
                    Tastes::SOUR => 'recipe.taste.sour',
                    Tastes::SPICY => 'recipe.taste.spicy',
                    Tastes::SALTY => 'recipe.taste.salty'
                ]
            ])
            ->add('start', 'hidden', [
                'required' => false,
                'empty_data' => 0,
                'constraints' => [
                    new Type([
                       'type' => 'integer' 
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0
                    ])
                ]
            ]);
        
        $builder->setMethod('GET');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return self::NAME;
    }
}