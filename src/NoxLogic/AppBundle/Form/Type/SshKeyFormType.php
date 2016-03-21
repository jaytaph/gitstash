<?php

namespace NoxLogic\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Create a new repository form
 */
class SshKeyFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, array(
                'attr' => array('placeholder' => 'A label to recognize the key'),
            ))
            ->add('key', TextareaType::class, array(
                'attr' => array('placeholder' => 'Starts with ssh-rsa', 'rows' => 10),
            ))
        ;
    }

}
