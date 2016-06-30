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

    /**
     * Returns an array with contacts form representation
     * 
     * @return array
     */
    public function getContactsFormRepresentation();

    /**
     * Returns an array with contacts email
     * 
     * @return array
     */
    public function getContactsEmail();
}