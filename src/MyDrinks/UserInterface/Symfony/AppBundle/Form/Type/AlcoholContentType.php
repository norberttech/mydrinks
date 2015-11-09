<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

final class AlcoholContentType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'alcohol_content';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('from', 'integer', [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                    ])
                ]
            ])
            ->add('to', 'integer', [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                    ])
                ]
            ]);
    }
}