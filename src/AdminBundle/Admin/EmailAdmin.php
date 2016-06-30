<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EmailAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'EmailAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-envelope-o"></i>';

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'created'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('tab.email')
                ->with('box.email')
                    ->add('emailFrom', 'email', array('required' => false))
                    ->add('subject')
                    ->add('body', 'textarea', array(
                        'attr' => array(
                            'class' => 'ckeditor'
                        ),
                        'required' => false
                    ))
                ->end()
            ->end()
            ->tab('tab.contacts')
                ->with('box.contacts')
                    ->add('contacts', 'admin_contacts', array('required' => false))
                ->end()
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->addIdentifier('subject')
            ->add('sent', 'datetime', array('format' => 'd/m/Y, H:i'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created')
            ->add('subject')
            ->add('sent')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('sent', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('emailFrom')
            ->add('subject')
            ->add('body', null, array('safe' => true))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if ($this->hasRole('ROLE_EMAIL_ADMIN')) {
            $actions['send_email'] = array(
                'label' => 'batch_action_send_email',
                'translation_domain' => $this->translationDomain,
                'ask_confirmation' => true
            );
        }

        return $actions;
    }

    public function hasRole($role)
    {
        $tokenStorage = $this->getConfigurationPool()->getContainer()->get('security.token_storage');
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        return null !== $tokenStorage->getToken() && $authorizationChecker->isGranted($role);
    }
}