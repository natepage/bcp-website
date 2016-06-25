<?php

namespace UserBundle\Security;

interface RolesTransformerInterface
{
    /**
     * Transform a roles list
     *
     * @param array $roles
     * @param array $exclude
     * @param boolean $withHeader
     *
     * @return array
     */
    public function transformRolesList(array $roles, array $exclude = array(), $withHeader = false);

    /**
     * Transform a role
     *
     * @param string $role
     * @param boolean $withHeader
     *
     * @throws \InvalidArgumentException
     *
     * @return string|array
     */
    public function transformRole($role, $withHeader = false);

    /**
     * Transform an admin role
     *
     * @param string $role
     * @param string $admin
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function transformAdminRole($admin, $role);
}