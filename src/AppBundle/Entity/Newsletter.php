<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\NewsletterRepository")
 */
class Newsletter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * For sonata's form rendering.
     */
    public function __toString()
    {
        return $this->getMail() ? $this->getMail() . ' [' . $this->getToken() . ']' : '';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Newsletter
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Newsletter
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    public static function getSearchFields()
    {
        return array(
            'n.mail' => 'text',
        );
    }

    public static function getDefaultPaginationField()
    {
        return array(
            'defaultSortFieldName' => 'n.mail',
            'defaultSortDirection' => 'desc'
        );
    }
}
