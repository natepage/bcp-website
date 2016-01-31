<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
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

            ->add('description', 'text', array(
                'label' => 'Description',
                'attr' => array(
                    'class' => 'form-control'
                ),//attr
                'required' => false
            ))//description

            ->add('content', 'textarea', array(
                'label' => 'Contenu',
                'attr' => array(
                    'class' => 'form-control ckeditor'
                ),//attr
                'required' => false
            ))//content

            ->add('priority', 'integer', array(
                'label' => 'Priorité',
                'attr' => array(
                    'class' => 'form-control'
                ),//attr
            ))//description

            ->add('published', 'checkbox', array(
                'label' => 'Publiée',
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
            'data_class' => 'AppBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_page';
    }
}
