<?php

namespace AppBundle\Utils\Newsletter;

class Contact implements ContactInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $token;

    /**
     * @var boolean
     */
    private $unSubscribable;

    public function __construct()
    {
        $this->username = '';
        $this->token = '';
        $this->unSubscribable = false;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isUnSubscribable()
    {
        return $this->unSubscribable;
    }

    /**
     * @param boolean $unSubscribable
     */
    public function setUnSubscribable($unSubscribable)
    {
        $this->unSubscribable = $unSubscribable;

        return $this;
    }
}