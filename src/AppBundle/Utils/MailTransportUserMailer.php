<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 02/09/2015
 * Time: 19:41
 */

namespace AppBundle\Utils;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MailTransportUserMailer implements MailerInterface
{
    protected $mailer;
    protected $router;
    protected $templating;
    protected $translator;
    protected $parameters;

    public function __construct(UrlGeneratorInterface  $router, EngineInterface $templating, TranslatorInterface $translator, array $parameters)
    {
        $transport = \Swift_MailTransport::newInstance();
        $this->mailer = \Swift_Mailer::newInstance($transport);

        $this->router = $router;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);
        $subject = $this->translate('registration.email.subject', $user->getUsername(), $url);
        $body = $this->translate('registration.email.message', $user->getUsername(), $url);

        $rendered = $this->templating->render('@App/Utils/email_structure.html.twig', array(
            'body' => $body
        ));

        $this->sendEmailMessage($rendered, $subject, $this->parameters['from_email']['confirmation'], $user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        $subject = $this->translate('resetting.email.subject', $user->getUsername(), $url);
        $body = $this->translate('resetting.email.message', $user->getUsername(), $url);

        $rendered = $this->templating->render('@App/Utils/email_structure.html.twig', array(
            'body' => $body
        ));

        $this->sendEmailMessage($rendered, $subject, $this->parameters['from_email']['resetting'], $user->getEmail());
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $subject, $fromEmail, $toEmail)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($renderedTemplate, 'text/html', 'utf-8');

        $this->mailer->send($message);
    }

    private function translate($stringId, $username, $url)
    {
        $parameters = array('%username%' => $username, '%confirmationUrl%' => $url);

        return $this->translator->trans($stringId, $parameters, 'FOSUserBundle');
    }
}