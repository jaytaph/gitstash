<?php

namespace NoxLogic\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Create a new repository form
 */
class RepoFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array('placeholder' => 'Repository name'),
            ))
            ->add('description', TextType::class, array(
                'required' => false,
                'attr' => array('placeholder' => 'Description'),
            ))
            ->add('visibility', ChoiceType::class, array(
                'attr' => array('placeholder' => 'Visibility'),
                'expanded' => true,
                'choices' => array(
                    'private' => 'Private repository',
                    'public' => 'Public repository',
                ),
                'mapped' => false,
            ))
            ->add('initialize', ChoiceType::class, array(
                'attr' => array('placeholder' => 'Initialize repository?'),
                'choices' => array('yes', 'no'),
                'mapped' => false,
            ))
        ;
    }

}
