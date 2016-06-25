<?php

namespace UserBundle\Twig;

use UserBundle\Security\RolesTransformerInterface;

class UserExtension extends \Twig_Extension
{
    /**
     * @var RolesTransformerInterface
     */
    private $rolesTransformer;

    public function __construct(RolesTransformerInterface $rolesTransformer)
    {
        $this->rolesTransformer = $rolesTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('user_render_roles', array($this, 'transformRolesList'))
        );
    }

    /**
     * Transform the roles list
     *
     * @param array $roles
     * @param array $exclude
     *
     * @return array
     */
    public function transformRolesList(array $roles, array $exclude = array())
    {
        return $this->rolesTransformer->transformRolesList($roles, $exclude);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_extension';
    }
}