<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ImageAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'ImageAdmin';

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-picture-o"></i>';

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('url')
            ->add('alt')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $fieldFileOptions = array(
            'required' => false,
            'attr' => array(
                'class' => 'admin-image-preview'
            )
        );
        $fieldCheckboxOptions = array(
            'attr' => array(
                'class' => 'default-preview-image'
            ),
            'mapped' => false,
            'required' => false
        );

        $image = $this->getSubject();

        if($image && null !== ($id = $image->getId())){
            //File
            if($webPath = $image->getWebPath()){
                $fullPath = $this->getRequest()->getBasePath() . '/' . $webPath;
                $fieldFileOptions['attr']['path'] = $fullPath;
            }

            //Checkbox
            $fieldCheckboxOptions['value'] = $image->getId();

            $defaultImage = $image->getPost()->getPreviewImage();
            if($image->getId() === $defaultImage->getId()){
                $fieldCheckboxOptions['data'] = true;
                $fieldCheckboxOptions['attr']['checked-from-server'] = true;
            }
        } else {
            $fieldCheckboxOptions['value'] = -1;
        }

        $formMapper
            ->add('file', 'file', $fieldFileOptions)
            ->add('defaultPreviewImage', 'checkbox', $fieldCheckboxOptions)
        ;
    }
}