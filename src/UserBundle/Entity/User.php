<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="author")
     */
    protected $posts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="author")
     */
    protected $pages;

    /**
     * @var string
     *
     * @ORM\Column(name="licence", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = "10",
     *      max = "10",
     *      exactMessage = "Votre numéro de licence doit faire {{ limit }} caractères. En cas de problème persistent, laissez le champs vide vous pourrez le renseigner plus tard à partir de votre profil."
     * )
     */
    protected $licence;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     */
    protected $newsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_access_token", type="text", nullable=true)
     */
    protected $fbAccessToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->posts = new ArrayCollection();
        $this->enabled = true;
        $this->locked = false;
        $this->newsletter = true;
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
     * Add posts
     *
     * @param \AppBundle\Entity\Post $posts
     * @return User
     */
    public function addPost(\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \AppBundle\Entity\Post $posts
     */
    public function removePost(\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add pages
     *
     * @param \AppBundle\Entity\Page $pages
     * @return User
     */
    public function addPage(\AppBundle\Entity\Page $pages)
    {
        $this->pages[] = $pages;

        return $this;
    }

    /**
     * Remove pages
     *
     * @param \AppBundle\Entity\Page $pages
     */
    public function removePage(\AppBundle\Entity\Page $pages)
    {
        $this->pages->removeElement($pages);
    }

    /**
     * Get pages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Get licence
     *
     * @param null $licence
     * @return User
     */
    public function setLicence($licence = null)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return string
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set newsletter
     *
     * @param $newsletter
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    public static function getSearchFields()
    {
        return array(
            'u.username' => 'text',
            'u.email' => 'text',
            'u.licence' => 'text',
        );
    }

    public static function getDefaultPaginationField()
    {
        return array(
            'defaultSortFieldName' => 'u.username',
            'defaultSortDirection' => 'asc'
        );
    }

    /**
     * Set fbAccessToken
     *
     * @param string $fbAccessToken
     * @return User
     */
    public function setFbAccessToken($fbAccessToken)
    {
        $this->fbAccessToken = $fbAccessToken;

        return $this;
    }

    /**
     * Get fbAccessToken
     *
     * @return string 
     */
    public function getFbAccessToken()
    {
        return $this->fbAccessToken;
    }
}
