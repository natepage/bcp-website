<?php

namespace AppBundle\Utils\Newsletter;

use Doctrine\Common\Persistence\ObjectManager;

class ContactProvider implements ContactProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var boolean
     */
    private $isSuperAdmin;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->isSuperAdmin = false;
    }

    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    public function getContacts()
    {
        if($this->isSuperAdmin){
            $user = $this->om->getRepository('UserBundle:User')->find(1);

            $contact = new Contact();
            $contact->setUsername($user->getUsername())
                    ->setEmail($user->getEmail());

            return array($contact);
        }

        $contacts = array();
        $emails = array();

        foreach($this->getUsers() as $user){
            if(!in_array($email = $user->getEmail(), $emails)){
                $emails[] = $email;

                $contact = new Contact();
                $contact->setEmail($email)
                        ->setUsername($user->getUsername());

                $contacts[] = $contact;
            }
        }

        foreach($this->getNewsletters() as $newsletter){
            if(!in_array($email = $newsletter->getMail(), $emails)){
                $emails[] = $email;

                $contact = new Contact();
                $contact->setEmail($email)
                        ->setToken($newsletter->getToken())
                        ->setUnSubscribable(true);

                $contacts[] = $contact;
            }
        }

        return $contacts;
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

    private function getUsers()
    {
        return $this->om
            ->getRepository('UserBundle:User')
            ->findBy(array('newsletter' => 1));
    }

    private function getNewsletters()
    {
        return $this->om
            ->getRepository('AppBundle:Newsletter')
            ->findAll();
    }
}