<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SharedFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Titre',
                'attr' => array(
                    'class' => 'form-control'
                )//attr
            ))//title

            ->add('to', 'email', array(
                'label' => 'Destinataire',
                'attr' => array(
                    'class' => 'form-control'
                )//attr
            ))//to

            ->add('file', new FileType(), array(
                'label' => 'Fichier',
                'required' => false
            ))//file

            ->add('submitted', 'checkbox', array(
                'label' => 'Partagé',
                'attr' => array(
                    'style' => 'margin-left: 10px;'
                ),//attr
                'required' => false
            ))//published
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SharedFile'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_sharedfile';
    }
}
