<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Page;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class PageAdmin extends AbstractAdmin
{
    protected $translationDomain = 'PageAdmin';

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'created'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.tab_page', array(
                'class' => 'col-md-10'
            ))
                ->add('title', 'text')
                ->add('description', 'text')
                ->add('content', 'textarea', array(
                    'attr' => array(
                        'class' => 'ckeditor'
                    ),
                    'required' => false
                ))
            ->end()
            ->with('form.tab_publish', array(
                'class' => 'col-md-2'
            ))
                ->add('priority', 'number')
                ->add('published', 'checkbox', array(
                    'required' => false
                ))
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->addIdentifier('title')
            ->add('description')
            ->add('authorName')
            ->add('published')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created')
            ->add('title')
            ->add('authorName')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('tab_page', array(
                'class' => 'col-md-8'
            ))
                ->add('title')
                ->add('description')
                ->add('content', null, array('safe' => true))
            ->end()
            ->with('tab_publish', array(
                'class' => 'col-md-4'
            ))
                ->add('priority')
                ->add('published')
            ->end()
        ;
    }

    /**
     * This function is called before the page's creating.
     *
     * @param $page
     */
    public function preValidate($page)
    {
        $this->setSlug($page);
    }

    /**
     * This function is called before the page's updating.
     *
     * @param mixed $page
     */
    public function preUpdate($page)
    {
        $this->setSlug($page);
    }

    /**
     * Slugify the page's title.
     *
     * @param Page $page
     */
    private function setSlug(Page $page)
    {
        $title = $page->getTitle();
        $slugify = $this->getConfigurationPool()->getContainer()->get('slugify');

        $page->setSlug($slugify->slugify($title));
    }
}