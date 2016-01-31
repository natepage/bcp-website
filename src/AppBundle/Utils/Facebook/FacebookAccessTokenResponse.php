<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 05/09/2015
 * Time: 19:46
 */

namespace AppBundle\Utils\Facebook;

class FacebookAccessTokenResponse
{
    private $accessToken;
    private $exception;

    public function __construct($accessToken = null, $exception = null)
    {
        $this->accessToken = $accessToken;
        $this->exception = $exception;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setException($exception)
    {
        $this->exception = $exception;

        return $this;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function hasError()
    {
        return $this->exception !== null;
    }

    public function tokenIsEmpty()
    {
        return $this->accessToken === null || $this->accessToken == '';
    }
}