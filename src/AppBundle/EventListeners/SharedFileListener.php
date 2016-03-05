<?php

namespace AppBundle\EventListeners;

use AppBundle\Events\BCPEvents;
use AppBundle\Events\SubmittedSharedFileEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class SharedFileListener implements EventSubscriberInterface
{
    private $router;
    private $templating;
    private $mailer;
    private $mailAddress;

    public function __construct(UrlGeneratorInterface  $router, EngineInterface $templating, $mailAddress)
    {
        $transport = \Swift_MailTransport::newInstance();
        $this->mailer = \Swift_Mailer::newInstance($transport);
        $this->mailAddress = $mailAddress;

        $this->router = $router;
        $this->templating = $templating;
    }

    public static function getSubscribedEvents()
    {
        return array(
            BCPEvents::SUBMITTED_EVENT => 'onSubmittedSharedFile'
        );
    }

    public function onSubmittedSharedFile(SubmittedSharedFileEvent $event)
    {
        $sharedFile = $event->getSharedFile();
        $userFrom = $event->getUser();

        $subject = sprintf('[BCP] %s vous a partagé un fichier', $userFrom->getUsername());

        $rendered = $this->templating->render('@App/Admin/SharedFile/submitted_email.html.twig', array(
            'sharedFile' => $sharedFile,
            'userFrom' => $userFrom,
            'fileLink' => $this->router->generate('front_sharedfile_get', array('token' => $sharedFile->getToken()))
        ));

        $this->sendEmailMessage($rendered, $subject, $this->mailAddress, $sharedFile->getTo());
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    private function sendEmailMessage($renderedTemplate, $subject, $fromEmail, $toEmail)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($renderedTemplate, 'text/html', 'utf-8');

        $this->mailer->send($message);
    }
}