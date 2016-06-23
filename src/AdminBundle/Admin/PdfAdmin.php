<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class PdfAdmin extends AbstractAdmin
{
    protected $translationDomain = 'PdfAdmin';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $fieldFileOptions = array(
            'required' => false,
            'attr' => array(
                'class' => 'admin-pdf-preview'
            )
        );

        $pdf = $this->getSubject();

        if($pdf && null !== ($id = $pdf->getId())){
            $fieldFileOptions['attr']['admin-pdf-alt'] = $pdf->getAlt();
        }

        $formMapper
            ->add('file', 'file', $fieldFileOptions)
        ;
    }
}