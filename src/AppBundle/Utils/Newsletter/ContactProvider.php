<?php

namespace AppBundle\Utils\Newsletter;

use Doctrine\Common\Persistence\ObjectManager;

class ContactProvider implements ContactProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function getContacts()
    {
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

    private function getUsers()
    {
        return $this->om
            ->getRepository('AppBundle:User')
            ->findBy(array('newsletter' => 1));
    }

    private function getNewsletters()
    {
        return $this->om
            ->getRepository('AppBundle:Newsletter')
            ->findAll();
    }
}