<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PostRepository")
 */
class Post
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
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="author_name", type="string", length=255)
     */
    private $authorName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="post", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $images;

    /**
     * @var integer
     *
     * @ORM\Column(name="preview_image_key", type="integer", options={"default": -1})
     */
    private $previewImageKey;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pdf", mappedBy="post", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pdfs;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="shared_newsletter", type="datetime", nullable=true)
     */
    private $sharedNewsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_id", type="text", length=255, nullable=true)
     */
    private $fbId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->published = false;
        $this->views = 0;
        $this->images = new ArrayCollection();
        $this->previewImageKey = -1;
        $this->pdfs = new ArrayCollection();
        $this->fbId = null;
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
     * Set created
     *
     * @param \DateTime $created
     * @return Post
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Post
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set authorName
     *
     * @param string $authorName
     * @return Post
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * Get authorName
     *
     * @return string 
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Post
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param $slug
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     * @return Post
     */
    public function setAuthor(\AppBundle\Entity\User $author = null)
    {
        $this->author = $author;

        if(null !== $author){
            $this->setAuthorName($author->getUsername());
            $author->addPost($this);
        }

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Post
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set views
     *
     * @param $views
     * @return Post
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add views
     *
     * @return Post
     */
    public function addViews()
    {
        $this->views = $this->views + 1;

        return $this;
    }

    /**
     * Set sharedNewsletter
     *
     * @param null $sharedNewsletter
     * @return Post
     */
    public function setSharedNewsletter($sharedNewsletter = null)
    {
        $this->sharedNewsletter = $sharedNewsletter;

        return $this;
    }

    /**
     * Get sharedNewsletter
     *
     * @return \DateTime
     */
    public function getSharedNewsletter()
    {
        return $this->sharedNewsletter;
    }

    public function isOwn($id){
        return $this->author !== null && $this->author->getId() === $id;
    }

    public static function getSearchFields()
    {
        return array(
            'p.title' => 'text',
            'p.description' => 'text',
            'p.content' => 'text'
        );
    }

    public static function getDefaultPaginationField()
    {
        return array(
            'defaultSortFieldName' => 'p.created',
            'defaultSortDirection' => 'desc'
        );
    }

    /**
     * Add images
     *
     * @param \AppBundle\Entity\Image $images
     * @return Post
     */
    public function addImages(\AppBundle\Entity\Image $images)
    {
        if(null !== $images){
            $this->images[] = $images;
            $images->setPost($this);
        }

        return $this;
    }

    /**
     * Remove images
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImages(\AppBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Set images
     *
     * @param Collection $images
     * @return Post
     */
    public function setImages(Collection $images)
    {
        foreach($images as $image){
            if(null !== $image){
                $image->setPost($this);
            } else {
                $images->removeElement($image);
            }
        }

        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set fbId
     *
     * @param string $fbId
     * @return Post
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;

        return $this;
    }

    /**
     * Get fbId
     *
     * @return string 
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * Add pdfs
     *
     * @param \AppBundle\Entity\Pdf pdfs
     * @return Post
     */
    public function addPdfs(\AppBundle\Entity\Pdf $pdfs)
    {
        if(null !== $pdfs){
            $this->pdfs[] = $pdfs;
            $pdfs->setPost($this);
        }

        return $this;
    }

    /**
     * Remove pdfs
     *
     * @param \AppBundle\Entity\Pdf $pdfs
     */
    public function removePdfs(\AppBundle\Entity\Pdf $pdfs)
    {
        $this->pdfs->removeElement($pdfs);
    }

    /**
     * Set pdfs
     *
     * @param Collection $pdfs
     * @return Post
     */
    public function setPdfs(Collection $pdfs)
    {
        foreach($pdfs as $pdf){
            if(null !== $pdf){
                $pdf->setPost($this);
            } else {
                $pdfs->removeElement($pdf);
            }
        }

        $this->pdfs = $pdfs;

        return $this;
    }

    /**
     * Get pdfs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPdfs()
    {
        return $this->pdfs;
    }

    /**
     * Set preview image key
     *
     * @param $previewImageKey
     * @return Post
     */
    public function setPreviewImageKey($previewImageKey)
    {
        $this->previewImageKey = $previewImageKey;

        return $this;
    }

    /**
     * Get preview image key
     *
     * @return int
     */
    public function getPreviewImageKey()
    {
        return $this->previewImageKey;
    }

    /**
     * Get preview image
     *
     * @return Image
     */
    public function getPreviewImage()
    {
        if($this->images->containsKey($this->previewImageKey)){
            return $this->images->get($this->previewImageKey);
        }

        return $this->images->first();
    }
}
