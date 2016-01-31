<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', 'text', array(
                'label' => 'Sujet',
                'attr' => array(
                    'class' => 'form-control'
                )//attr
            ))//subject

            ->add('from', 'hidden')

            ->add('to', 'text', array(
                'label' => 'Destinataire',
                'attr' => array(
                    'class' => 'form-control'
                )//attr
            ))//to
            ->add('body', 'textarea', array(
                'label' => 'Corps du mail',
                'attr' => array(
                    'class' => 'form-control ckeditor'
                )//attr
            ))//body
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_email';
    }
}
