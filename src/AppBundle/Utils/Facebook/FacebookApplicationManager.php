<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 05/09/2015
 * Time: 19:34
 */

namespace AppBundle\Utils\Facebook;

use AppBundle\Entity\Post;
use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FacebookApplicationManager
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $defaultGraphVersion;

    /**
     * @var string
     */
    private $pageId;

    /**
     * @var \Facebook\Facebook
     */
    private $application;

    /**
     * @var \Facebook\Helpers\FacebookRedirectLoginHelper
     */
    private $helper;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorageInterface;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper
     */
    private $assetsHelper;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @var \Facebook\Authentication\AccessToken
     */
    private $tmpAccessToken;

    public function __construct(
        $id,
        $secret,
        $defaultGraphVersion,
        $pageId,
        TokenStorageInterface $tokenStorageInterface,
        AssetsHelper $assetsHelper,
        Router $router
    )
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->defaultGraphVersion = $defaultGraphVersion;
        $this->pageId = $pageId;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->assetsHelper = $assetsHelper;
        $this->router = $router;
        $this->tmpAccessToken = null;

        $this->init();
    }

    public function init()
    {
        $this->application = new Facebook(array(
            'app_id' => $this->id,
            'app_secret' => $this->secret,
            'default_graph_version' => $this->defaultGraphVersion
        ));

        $this->helper = $this->application->getRedirectLoginHelper();
    }

    public function getUserAccessToken()
    {
        $response = new FacebookAccessTokenResponse();

        if($this->tmpAccessToken === null){
            $user = $this->getUser();

            if($user->getFbAccessToken() === null || $user->getFbAccessToken() == ''){
                try {
                    $accessToken = $this->getAccessToken();
                    $response->setAccessToken($accessToken);
                } catch(\Exception $e) {
                    $response->setException($e);
                }
            } else {
                $client = $this->getClient();

                if($client->debugToken($user->getFbAccessToken())->getIsValid()){
                    $response->setAccessToken($user->getFbAccessToken());
                } else {
                    try {
                        $accessToken = $this->getAccessToken();
                        $response->setAccessToken($accessToken);
                    } catch(\Exception $e) {
                        $response->setException($e);
                    }
                }
            }
        } else {
            $response->setAccessToken($this->tmpAccessToken);
        }

        return $response;
    }

    public function getUserLongAccessToken()
    {
        $response = new FacebookAccessTokenResponse();

        if($this->tmpAccessToken === null){
            $user = $this->getUser();

            if($user->getFbAccessToken() === null || $user->getFbAccessToken() == ''){
                try {
                    $accessToken = $this->getAccessToken('long');
                    $response->setAccessToken($accessToken);
                } catch(\Exception $e) {
                    $response->setException($e);
                }
            } else {
                $client = $this->getClient();

                if($client->debugToken($user->getFbAccessToken())->getIsValid()){
                    $response->setAccessToken($user->getFbAccessToken());
                } else {
                    try {
                        $accessToken = $this->getAccessToken('long');
                        $response->setAccessToken($accessToken);
                    } catch(\Exception $e) {
                        $response->setException($e);
                    }
                }
            }
        } else {
            $response->setAccessToken($this->tmpAccessToken);
        }

        return $response;
    }

    public function getClient()
    {
        return $this->application->getOAuth2Client();
    }

    public function getLoginUrl($url)
    {
        $permissions = array(
            'manage_pages',
            'publish_pages'
        );

        return $this->helper->getLoginUrl($url, $permissions);
    }

    private function getAccessToken($time = 'short')
    {
        switch($time){
            case 'short':
                $accessToken = $this->helper->getAccessToken();
                break;
            case 'long':
                $accessToken = $this->getClient()->getLongLivedAccessToken($this->helper->getAccessToken());
                break;
            default:
                $accessToken = null;
                break;
        }

        return $accessToken;
    }

    public function publishOnPage(Post $post, $message = null)
    {
        $response = new FacebookPostAsPageResponse();
        $accessToken = $this->getUserLongAccessToken();

        if(!$post->getPublished()){
            return $response->setException(new \Exception('flash_batch_facebook_post_not_published'));
        }

        if($accessToken->tokenIsEmpty()){
            return $response->setException(new \Exception('flash_batch_facebook_access_token_empty'));
        }

        $this->application->setDefaultAccessToken($accessToken->getAccessToken());

        try {
            $getPageAccessToken = $this->application
                                       ->sendRequest('GET', '/' . $this->pageId, array('fields' => 'access_token'))
                                       ->getDecodedBody();

            $params = array(
                'message' => (null !== $message) ? $message : '',
                'name' => $post->getTitle(),
                'caption' => $post->getDescription(),
                'link' => $this->router->generate('front_article_view', array('slug' => $post->getSlug()), true)
            );

            if(count($post->getImages()) > 0){
                $hompage = $this->router->generate('homepage', array(), true);
                $imgWebPath = $this->assetsHelper->getUrl($post->getPreviewImage()->getWebPath());
                $params['picture'] = $hompage . $imgWebPath;
            }

            $endPoint = (null === $post->getFbId()) ? $this->pageId . '/feed' : $post->getFbId();

            $postAsPage = $this->application
                               ->post('/' . $endPoint, $params, $getPageAccessToken['access_token'])
                               ->getDecodedBody();

            $response->setId(isset($postAsPage['id']) ? $postAsPage['id'] : $post->getFbId());
        } catch(\Exception $e) {
            return $response->setException($e);
        }

        return $response;
    }

    public function publishListOnPage(array $posts)
    {
        $responses = array(
            'errors' => array()
        );

        foreach($posts as $post){
            $postAsPage = $this->publishOnPage($post);

            if($postAsPage->hasError()){
                $responses['errors'][] = array(
                    'message' => $postAsPage->getException()->getMessage(),
                    'post' => $post->getTitle()
                );
            } else {
                $post->setFbId($postAsPage->getId());
            }
        }

        return $responses;
    }

    private function getUser()
    {
        return $this->tokenStorageInterface->getToken()->getUser();
    }
}