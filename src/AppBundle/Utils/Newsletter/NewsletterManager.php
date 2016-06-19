<?php

namespace AppBundle\Utils\Newsletter;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class NewsletterManager implements NewsletterManagerInterface
{
    /**
     * @var ContactProviderInterface
     */
    private $contactProvider;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $from;

    /**
     * Constructor
     */
    public function __construct(ContactProviderInterface $contactProvider, EngineInterface $templating, $from)
    {
        $this->contactProvider = $contactProvider;
        $this->templating = $templating;
        $this->from = $from;
    }

    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->contactProvider->setIsSuperAdmin($isSuperAdmin);
    }

    public function share(Post $post)
    {
        if(!$post->getPublished()){
            return null;
        }

        $contacts = $this->contactProvider->getContacts();

        $now = new \DateTime();
        $subject = sprintf("[%s] Des nouveautés sur le site du BCP", $now->format('d-m-Y'));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from);

        $transport = \Swift_MailTransport::newInstance();
        $mailer = \Swift_Mailer::newInstance($transport);
        
        foreach($contacts as $contact){
            if(!$contact instanceof ContactInterface){
                throw new \InvalidArgumentException(sprintf("%s must implement %s.", get_class($contact), ContactInterface::class));
            }

            $template = $this->templating->render('@App/Admin/Post/newsletter.html.twig', array(
                'post' => $post,
                'contact' => $contact
            ));

            $message->setTo(array($contact->getEmail()));
            $message->setBody($template, 'text/html', 'utf-8');

            if(!$mailer->send($message)){
                return null;
            }
        }

        return $now;
    }
}