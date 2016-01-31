<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 20/08/2015
 * Time: 12:12
 */

namespace AppBundle\Utils;

class ExternalUrlGenerator
{
    const SEPARATOR_EXTERNE_FIRST = "?";
    const SEPARATOR_EXTERNE_SECOND = "&";

    private $url;
    private $arguments = array();

    public function __construct($url = null, $arguments = array())
    {
        $this->url = $url;
        $this->arguments = array_merge($this->arguments, $arguments);
    }

    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setArgument($key, $value = null)
    {
        if(null !== $value){
            $this->arguments[$key] = $value;
        }

        return $this;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function addArguments(array $arguments)
    {
        foreach($arguments as $key => $value){
            $this->setArgument($key, $value);
        }

        return $this;
    }

    public function removeArgument($key)
    {
        if(array_key_exists($key, $this->arguments)){
            unset($this->arguments[$key]);
        }

        return $this;
    }

    public function generate()
    {
        $urlResult = $this->url . self::SEPARATOR_EXTERNE_FIRST . http_build_query($this->arguments,'',self::SEPARATOR_EXTERNE_SECOND);

        return $urlResult;
    }
}