<?php

namespace AdminBundle\Admin;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'UserAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-users"></i>';

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'username',
    );

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('tab_user', array(
                'class' => 'col-md-8'
            ))
                ->add('username', 'text')
                ->add('email', 'email')
                ->add('licence', 'text', array('required' => false))
                ->add('plainPassword', 'text', array(
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                ))
            ->end()
            ->with('tab_status', array(
                'class' => 'col-md-4'
            ))
                ->add('enabled', 'checkbox', array('required' => false))
                ->add('locked', 'checkbox', array('required' => false))
                ->add('expired', 'checkbox', array('required' => false))
                ->add('credentialsExpired', 'checkbox', array('required' => false))
            ->end()
            ->with('tab_roles', array(
                'class' => 'col-md-12'
            ))
                ->add('roles', 'user_admin_roles')
            ->end()
        ;

        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $formMapper
                ->with('tab_status')
                    ->add('newsletter', 'checkbox', array('required' => false))
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled')
            ->add('lastLogin', 'datetime', array('format' => 'd/m/Y, H:i'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('enabled')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('tab_user', array(
                'class' => 'col-md-8'
            ))
                ->add('username')
                ->add('email')
                ->add('licence')
            ->end()
            ->with('tab_status', array(
                'class' => 'col-md-4'
            ))
                ->add('enabled')
                ->add('locked')
                ->add('expired')
                ->add('credentialsExpired')
                ->add('newsletter')
                ->add('lastLogin', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->end()
            ->with('tab_roles', array(
                'class' => 'col-md-12'
            ))
                ->add('roles', 'array', array(
                    'display_label' => false,
                    'template' => 'UserBundle:CRUD/Show:field_roles.html.twig'
                ))
            ->end()
        ;
    }
    
    /**
     * Set userManager
     *
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }
}