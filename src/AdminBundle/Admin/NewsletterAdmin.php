<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class NewsletterAdmin extends AbstractAdmin
{
    protected $translationDomain = 'NewsletterAdmin';

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'mail'
    );

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct($code, $class, $baseControllerName, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('mail')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('mail')
            ->add('token')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('mail')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('mail')
            ->add('token')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        if($this->nativeIsGranted('ROLE_ADMIN')){
            $collection->clearExcept('list', 'show');
        }
    }

    /**
     * Check if the token isn't null before native isGranted calling, because the admin service is setting once
     * before firewalls setting so it throws an exception
     *
     * @param string $role
     * @return boolean
     */
    private function nativeIsGranted($role)
    {
        $token = $this->tokenStorage->getToken();
        $checker = $this->authorizationChecker;

        return null !== $token && $checker->isGranted($role);
    }
}