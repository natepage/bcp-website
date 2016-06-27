<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class PdfAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'PdfAdmin';

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-file-pdf-o"></i>';

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