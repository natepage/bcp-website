<?php

namespace AppBundle\Utils\Newsletter;

interface EmailSenderInterface
{
    /**
     * Send an email
     *
     * @param string $from
     * @param array $to
     * @param string $subject
     * @param string $body
     * @param string $format
     * @param string $encode
     *
     * @return boolean
     */
    public function send($from, array $to, $subject, $body, $format = 'text/html', $encode = 'utf-8');
}