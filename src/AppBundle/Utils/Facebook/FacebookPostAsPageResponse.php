<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 07/09/2015
 * Time: 23:03
 */

namespace AppBundle\Utils\Facebook;

class FacebookPostAsPageResponse
{
    private $id;
    private $exception;

    public function __construct($id = null,  \Exception $exception = null)
    {
        $this->id = $id;
        $this->exception = $exception;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setException(\Exception $exception)
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
}