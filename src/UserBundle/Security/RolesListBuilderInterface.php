<?php

namespace UserBundle\Security;

interface RolesListBuilderInterface
{
    /**
     * Build the roles list
     * 
     * @return array
     */
    public function buildRolesList();
}