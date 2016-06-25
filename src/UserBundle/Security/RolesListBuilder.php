<?php

namespace UserBundle\Security;

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

    public function __construct(array $rolesHierarchy, array $excludedAdmins, RolesTransformerInterface $rolesTransformer)
    {
        $this->rolesHierarchy = $rolesHierarchy;
        $this->excludedAdmins = $excludedAdmins;
        $this->rolesTransformer = $rolesTransformer;
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
     * Build the admin's roles list
     */
    private function buildExcludedRolesList()
    {
        $excludeRoles = $this->excludeRoles;

        foreach($this->excludedAdmins as $admin => $excludedRoles){
            $roles = empty($excludedRoles) ? $this->adminRoles : $excludedRoles;

            foreach($roles as $role){
                $excludeRoles[] = $this->rolesTransformer->transformAdminRole($admin, $role);
            }
        }

        return $excludeRoles;
    }
}