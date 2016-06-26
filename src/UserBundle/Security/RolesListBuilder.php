<?php

namespace UserBundle\Security;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RolesListBuilder implements RolesListBuilderInterface
{
    /**
     * @var array
     */
     private $adminRoles = array(
        'READER',
        'EDITOR',
        'ADMIN'
    );

    /**
     * @var array
     */
    private $excludeRoles = array(
        'ROLE_USER',
        'ROLE_INTERFACE_ADMIN',
        'ROLE_SUPER_ADMIN'
    );

    /**
     * @var array
     */
    private $rolesHierarchy;

    /**
     * @var array
     */
    private $excludedAdmins;

    /**
     * @var RolesTransformerInterface
     */
    private $rolesTransformer;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        array $rolesHierarchy,
        array $excludedAdmins,
        RolesTransformerInterface $rolesTransformer,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->rolesHierarchy = $rolesHierarchy;
        $this->excludedAdmins = $excludedAdmins;
        $this->rolesTransformer = $rolesTransformer;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRolesList()
    {
        $exclude = $this->buildExcludedRolesList();

        $rolesList = array();

        foreach($this->rolesHierarchy as $role => $sub){
            $rolesList[] = $role;
        }

        return $this->rolesTransformer->transformRolesList($rolesList, $exclude, true);
    }

    /**
     * Get excludeRoles
     */
    private function getExcludeRoles()
    {
        $excludeRoles = $this->excludeRoles;

        foreach($excludeRoles as $key => $role){
            if($role == 'ROLE_SUPER_ADMIN' && $this->authorizationChecker->isGranted($role)){
                unset($excludeRoles[$key]);
            }
        }

        return $excludeRoles;
    }

    /**
     * Build the admin's roles list
     */
    private function buildExcludedRolesList()
    {
        $excludeRoles = $this->getExcludeRoles();

        foreach($this->excludedAdmins as $admin => $excludedRoles){
            $roles = empty($excludedRoles) ? $this->adminRoles : $excludedRoles;

            foreach($roles as $role){
                $excludeRoles[] = $this->rolesTransformer->transformAdminRole($admin, $role);
            }
        }

        return $excludeRoles;
    }
}