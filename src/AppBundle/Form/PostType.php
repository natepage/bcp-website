<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
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

            ->add('images', 'collection', array(
                'type' => new ImageType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false
            ))//images

            ->add('previewImageKey', 'text', array(
                'required' => false,
                'empty_data' => -1
            ))//previewImageId

            ->add('pdfs', 'collection', array(
                'type' => new PdfType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false
            ))//pdfs

            ->add('published', 'checkbox', array(
                'label' => 'Publié',
                'attr' => array(
                    'style' => 'margin-left: 10px;'
                ),//attr
                'required' => false
            ))//published

            ->add('newsletter', 'checkbox', array(
                'label' => 'Diffuser à la newsletter',
                'attr' => array(
                    'style' => 'margin-left: 10px;'
                ),//attr
                'required' => false,
                'mapped' => false
            ))//newsletter

            ->add('facebook', 'checkbox', array(
                'label' => 'Diffuser sur la page Facebook',
                'attr' => array(
                    'style' => 'margin-left: 10px;'
                ),//attr
                'required' => false,
                'mapped' => false
            ))//facebook

            ->add('facebook_message', 'text', array(
                'label' => 'Message additionnel pour Facebook',
                'attr' => array(
                    'class' => 'form-control bcp-mb'
                ),//attr
                'required' => false,
                'mapped' => false
            ))//newsletter
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_post';
    }
}
