<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class AdminSearchType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_search';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['required' => false, 'label' => 'recipe.form.admin_search.name'])
            ->add('submit', 'submit', ['label' => 'recipe.form.admin_search.search']);
        
        $builder->setMethod('POST');
    }
}