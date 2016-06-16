<?php

namespace AppBundle\Utils\Newsletter;

use Doctrine\Common\Persistence\ObjectManager;

class DevContactProvider implements ContactProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var integer
     */
    private $id;

    public function __construct(ObjectManager $om, $id)
    {
        $this->om = $om;
        $this->id = $id;
    }

    public function getContacts()
    {
        $user = $this->om->getRepository('AppBundle:User')->find($this->id);

        if(null === $user){
            throw new \InvalidArgumentException(sprintf("User with id[%s] doesn't exist.", $this->id));
        }

        $contact = new Contact();
        $contact->setEmail($user->getEmail())
                ->setUsername($user->getUsername());

        return array($contact);
    }
}