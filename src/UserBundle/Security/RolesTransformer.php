<?php

namespace UserBundle\Security;

class RolesTransformer implements RolesTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transformRolesList(array $roles, array $exclude = array(), $withHeader = false)
    {
        $rolesList = array();

        foreach($roles as $role){
            if(in_array($role, $exclude)){
                continue;
            }

            $header = $this->getRoleHeader($role);
            $body = $this->getRoleBody($role);

            if(!$withHeader){
                $rolesList[$role] = $body;
            } else {
                $rolesList[$header][$role] = $body;
            }
        }

        return $rolesList;
    }

    /**
     * {@inheritdoc}
     */
    public function transformRole($role, $withHeader = false)
    {
        if(!is_scalar($role)){
            throw new \InvalidArgumentException(sprintf('Role argument must be a string, %s given.', gettype($role)));
        }

        if(!$withHeader){
            return $this->getRoleBody($role);
        }

        return array(
            $this->getRoleHeader($role) => $this->getRoleBody($role)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function transformAdminRole($admin, $role)
    {
        if(!is_scalar($admin) || !is_scalar($role)){
            throw new \InvalidArgumentException(sprintf('Admin and Role arguments must be strings, [%s, %s] given.', gettype($admin), gettype($role)));
        }

        return sprintf('ROLE_%s_%s', strtoupper($admin), strtoupper($role));
    }

    /**
     * Get the role header
     *
     * @param $role
     * @return string
     */
    private function getRoleHeader($role)
    {
        if(preg_match('#ROLE_([a-z0-9]+)#i', $role, $matches)){
            $role = $matches[1];
        }

        return strtolower(sprintf('header.%s', $role));
    }

    /**
     * Get the role render
     *
     * @param $role
     * @return string
     */
    private function getRoleBody($role)
    {
        if(preg_match('#ROLE_([a-z0-9._-]+)#i', $role, $matches)){
            $role = $matches[1];
        }

        return strtolower(sprintf('render.%s', $role));
    }
}