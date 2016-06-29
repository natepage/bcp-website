<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Post;
use AppBundle\Utils\Facebook\FacebookApplicationManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Doctrine\Common\Collections\ArrayCollection;

class PostAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'PostAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-newspaper-o"></i>';

    /**
     * @var string
     */
    public $flashFacebookIcon = '<i class="fa fa-3x fa-facebook-official"></i>';

    /**
     * @var string
     */
    public $flashNewsletterIcon = '<i class="fa fa-3x fa-send-o"></i>';

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'created'
    );

    /**
     * @var array
     */
    protected $accessMapping = array(
        'newsletter' => 'NEWSLETTER',
        'facebook' => 'FACEBOOK'
    );
    
    /**
     * @var ArrayCollection
     */
    private $oldImages;

    /**
     * @var ArrayCollection
     */
    private $oldPdfs;

    /**
     * @var FacebookApplicationManager
     */
    private $facebook;

    public function __construct($code, $class, $baseControllerName, FacebookApplicationManager $facebook)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->facebook = $facebook;
    }


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('tab_post', array(
                'class' => 'col-md-8'
            ))
                ->add('title', 'text')
                ->add('description', 'text')
                ->add('content', 'textarea', array(
                    'attr' => array(
                        'class' => 'ckeditor'
                    ),
                    'required' => false
                ))
            ->end()
            ->with('tab_publish', array(
                'class' => 'col-md-4'
            ))
                ->add('published', 'checkbox', array(
                    'required' => false
                ))
            ->end()
            ->with('tab_images', array(
                'class' => 'col-md-6',
                'description' => 'form.tab_description_images'
            ))
                ->add('images', 'sonata_type_collection', array(
                    'label' => false,
                    'by_reference' => false,
                    'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table'
                ))
            ->end()
            ->with('tab_pdfs', array(
                'class' => 'col-md-6'
            ))
                ->add('pdfs', 'sonata_type_collection', array(
                    'label' => false,
                    'by_reference' => false,
                    'cascade_validation' => true,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table'
                ))
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->addIdentifier('title')
            ->add('authorName')
            ->add('published')
            ->add('sharedNewsletter', 'datetime', array('format' => 'd/m/Y, H:i'))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created')
            ->add('title')
            ->add('authorName')
            ->add('published')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('tab_post', array(
                'class' => 'col-md-8'
            ))
                ->add('title')
                ->add('description')
                ->add('content', null, array('safe' => true))
            ->end()
            ->with('tab_publish', array(
                'class' => 'col-md-4'
            ))
                ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
                ->add('authorName')
                ->add('published')
                ->add('sharedNewsletter', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->end()
            ->with('show.tab_images', array(
                'class' => 'col-md-6'
            ))
                ->add('images', null, array(
                    'display_label' => false,
                    'template' => 'AdminBundle:CRUD/Show:field_images.html.twig'
                ))
            ->end()
            ->with('tab_pdfs', array(
                'class' => 'col-md-6'
            ))
                ->add('pdfs', null, array(
                    'display_label' => false,
                    'template' => 'AdminBundle:CRUD/Show:field_pdfs.html.twig'
                ))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if($this->hasRole('ROLE_POST_ADMIN')) {
            $actions['newsletter'] = array(
                'label' => 'batch_action_newsletter',
                'translation_domain' => $this->translationDomain,
                'ask_confirmation' => true
            );

            /*$actions['facebook'] = array(
                'label' => 'batch_action_facebook',
                'translation_domain' => $this->translationDomain,
                'ask_confirmation' => true
            );

            $actions['newsletter_and_facebook'] = array(
                'label' => 'batch_action_newsletter_and_facebook',
                'translation_domain' => $this->translationDomain,
                'ask_confirmation' => true
            );*/
        }

        return $actions;
    }

    public function hasRole($role)
    {
        $tokenStorage = $this->getConfigurationPool()->getContainer()->get('security.token_storage');
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        return null !== $tokenStorage->getToken() && $authorizationChecker->isGranted($role);
    }

    /**
     * This function is called before the post's creating.
     *
     * @param $post
     */
    public function preValidate($post)
    {
        $this->setSlug($post);
        $this->handleEmptyElementsGiven($post, array('images', 'pdfs'));
        $this->handlePreviewImageKey($post);
    }

    /**
     * This function is called before the post's updating.
     *
     * @param mixed $post
     */
    public function preUpdate($post)
    {
        $this->setSlug($post);
        $this->handleEmptyElementsGiven($post, array('images', 'pdfs'));
        $this->handlePreviewImageKey($post);
    }

    /**
     * This function is called before the post's removing.
     *
     * @param mixed $post
     */
    public function preRemove($post)
    {
        $this->removeImagesCache($post->getImages());
    }

    /**
     * This function is called just after the post was updated.
     *
     * @param mixed $post
     */
    public function postUpdate($post)
    {
        $this->handleRemovedElements($post, array(
            'images' => 'removeImagesCache',
            'pdfs'   => null
        ));
    }

    /**
     * Set oldImages
     *
     * @param ArrayCollection $oldImages
     */
    public function setOldImages(ArrayCollection $oldImages)
    {
        $this->oldImages = $oldImages;
    }

    /**
     * Get oldImages
     *
     * @return ArrayCollection
     */
    public function getOldImages()
    {
        return $this->oldImages;
    }

    /**
     * Set oldPdfs
     *
     * @param ArrayCollection $oldPdfs
     */
    public function setOldPdfs(ArrayCollection $oldPdfs)
    {
        $this->oldPdfs = $oldPdfs;
    }

    /**
     * Get oldPdfs
     *
     * @return ArrayCollection
     */
    public function getOldPdfs()
    {
        return $this->oldPdfs;
    }

    /**
     * Handle old elements.
     *
     * @param Post $post
     * @param array $fields
     */
    public function handleOldElements(Post $post, array $fields)
    {
        foreach($fields as $field){
            $this->setOldElements($post, $field);
        }
    }

    /**
     * Keep elements in memory for use it after the post's updating.
     *
     * @param Post $post
     */
    public function setOldElements(Post $post, $field)
    {
        $setter = 'setOld' . ucfirst($field);
        $getter = 'get' . ucfirst($field);

        if(method_exists($this, $setter) && method_exists($post, $getter)){
            $elements = $post->$getter();

            if(is_array($elements) || ($elements instanceof \ArrayAccess)){
                $oldElements = new ArrayCollection();

                foreach($post->$getter() as $element){
                    $oldElements->add($element);
                }

                $this->$setter($oldElements);
            } else {
                $this->$setter($elements);
            }
        }
    }

    /**
     * Handle removed elements.
     *
     * @param Post $post
     * @param array $fields
     */
    private function handleRemovedElements(Post $post, array $fields)
    {
        foreach($fields as $field => $callback){
            $this->deleteRemovedElements($post, $field, $callback);
        }
    }

    /**
     * Delete elements which are removed in form.
     *
     * @param Post $post
     * @param string $field
     * @param null|string $callback
     */
    private function deleteRemovedElements(Post $post, $field, $callback = null)
    {
        $oldGetter = 'getOld' . ucfirst($field);
        $currentGetter = 'get' . ucfirst($field);

        $removedElements = array();

        $oldElements = $this->$oldGetter();
        $currentElements = $post->$currentGetter();

        $modelManager = $this->getModelManager();

        foreach($oldElements as $element){
            if(false === $currentElements->contains($element)){
                if($callback !== null){
                    $removedElements[] = $element;
                } else {
                    $modelManager->delete($element);
                }
            }
        }

        if($callback !== null){
            $this->$callback($removedElements);
        }
    }

    /**
     * Remove Images representations in database and liip filters.
     *
     * @param mixed $images
     */
    private function removeImagesCache($images)
    {
        $imageManager = $this->getModelManager();
        $liipFiltersManager = $this->getConfigurationPool()->getContainer()->get('liip_imagine.cache.manager');

        foreach($images as $image){
            $liipFiltersManager->remove($image->getWebPath());
            $imageManager->delete($image);
        }
    }

    /**
     * Slugify the post's title.
     *
     * @param Post $post
     */
    private function setSlug(Post $post)
    {
        $title = $post->getTitle();
        $slugify = $this->getConfigurationPool()->getContainer()->get('slugify');

        $post->setSlug($slugify->slugify($title));
    }


    /**
     * Handle empty elements given when the user add empty element form.
     *
     * @param Post $post
     * @param array $fields
     */
    private function handleEmptyElementsGiven(Post $post, array $fields)
    {
        foreach($fields as $field){
            $this->removeEmptyElementsGiven($post, $field);
        }
    }

    /**
     * Remove empty elements given when the user add empty element form.
     *
     * @param Post $post
     * @param string $field
     */
    private function removeEmptyElementsGiven(Post $post, $field)
    {
        $getter = 'get' . ucfirst($field);

        $elements = $post->$getter();
        foreach($elements as $element){
            if(null === $element->getId() && null === $element->getFile()){
                $elements->removeElement($element);
            }
        }
    }

    /**
     * Set the preview image key.
     *
     * @param Post $post
     */
    private function handlePreviewImageKey(Post $post)
    {
        $uniqid = $this->getUniqid();
        $request = $this->getRequest();
        $datas = $request->request->get($uniqid);

        if(isset($datas['images'])){
            $images = $post->getImages();

            foreach($datas['images'] as $key => $image){
                if($images->containsKey($key) && isset($image['defaultPreviewImage'])){
                    $post->setPreviewImageKey($key);
                }
            }
        }
    }
}