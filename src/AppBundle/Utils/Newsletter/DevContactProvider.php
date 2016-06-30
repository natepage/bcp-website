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
        $user = $this->om->getRepository('UserBundle:User')->find($this->id);

        if(null === $user){
            throw new \InvalidArgumentException(sprintf("User with id[%s] doesn't exist.", $this->id));
        }

        $contact = new Contact();
        $contact->setEmail($user->getEmail())
                ->setUsername($user->getUsername());

        return array($contact);
    }

    public function getContactsFormRepresentation()
    {
        $formRepresentation = array();

        foreach($this->getContacts() as $contact){
            if(!array_key_exists($email = $contact->getEmail(), $formRepresentation)){
                $username = $contact->getUsername();

                if(null !== $username){
                    $render = sprintf('%s - %s', $username, $email);
                } else {
                    $render = $email;
                }

                $formRepresentation[$email] = $render;
            }
        }

        return $formRepresentation;
    }

    public function getContactsEmail()
    {
        $emails = array();

        foreach($this->getContacts() as $contact){
            if(!in_array($email = $contact->getEmail(), $emails)){
                $emails[] = $email;
            }
        }

        return $emails;
    }
}