<?php

namespace AppBundle\Utils\Newsletter;

interface ContactProviderInterface
{
    /**
     * Returns an array with contacts instances.
     * 
     * @return array
     */
    public function getContacts();
}