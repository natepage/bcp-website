<?php

namespace AppBundle\Utils\Newsletter;

class EmailSender implements EmailSenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function send($from, array $to, $subject, $body, $format = 'text/html', $encode = 'utf-8')
    {
        $mailer = $this->getMailer();
        $message = $this->getMessage();

        $message->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($body, $format, $encode);

        return $mailer->send($message);
    }

    private function getMessage()
    {
        return \Swift_Message::newInstance();
    }

    private function getMailer()
    {
        $transport = \Swift_MailTransport::newInstance();

        return \Swift_Mailer::newInstance($transport);
    }
}