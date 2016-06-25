<?php

namespace UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use UserBundle\DependencyInjection\UserExtension;

class UserBundle extends Bundle
{
    /**
     * @var string
     */
    protected $parent;

    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getContainerExtension()
    {
        return new UserExtension();
    }
}
