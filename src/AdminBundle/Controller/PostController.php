<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class PostController extends CRUDController
{
    public function preList(Request $request)
    {
        if($this->admin->hasRole('ROLE_POST_ADMIN')){
            $user = $this->getUser();
            $fb = $this->get('bcp.facebook');
            $cookieName = 'bcp_new_interface_newsletter';

            if(!$request->cookies->has($cookieName)){
                $this->addFlash('sonata_flash_info', $this->admin->trans('flash_cookie_new_interface_newsletter', array(
                    '%cookie_newsletter_url%' => $this->generateUrl('ajax_cookie_consent', array('cookieName' => $cookieName))
                )), $this->admin->flashNewsletterIcon);
            }

            if($fb->getUserLongAccessToken()->tokenIsEmpty()){
                $currentPath = $this->admin->generateUrl('list', $this->admin->getFilterParameters(), true);
                $fbLoginUrl = $fb->getLoginUrl($currentPath);

                $this->addFlash('sonata_flash_info', $this->admin->trans('flash_facebook_token_empty', array(
                    '%fb_login_url%' => $fbLoginUrl
                )), $this->admin->flashFacebookIcon);
            } else {
                $fbToken = (string) $fb->getUserLongAccessToken()->getAccessToken();

                $user->setFbAccessToken($fbToken);
                $this->admin->getModelManager()->update($user);
            }
        }
    }

    public function preCreate(Request $request, $post)
    {
        $post->setAuthor($this->getUser());
    }

    public function preEdit(Request $request, $post)
    {
        $this->admin->handleOldElements($post, array('images', 'pdfs'));
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $this->admin->checkAccess('batchDelete');

        $liipManager = $this->get('liip_imagine.cache.manager');
        $modelManager = $this->admin->getModelManager();
        $selectedPosts = $query->execute();

        try {
            foreach($selectedPosts as $post){
                foreach($post->getImages() as $image){
                    $liipManager->remove($image->getWebPath());
                }

                $modelManager->delete($post);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function batchActionNewsletter(ProxyQueryInterface $query)
    {
        if(!$this->admin->isGranted('ROLE_POST_ADMIN')){
            throw $this->createAccessDeniedException();
        }

        $selectedPosts = $query->execute();

        try {
            $this->shareNewsletter($selectedPosts);
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_batch_newsletter_error'), $this->admin->flashNewsletterIcon);
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function batchActionFacebook(ProxyQueryInterface $query)
    {
        if(!$this->admin->isGranted('ROLE_POST_ADMIN')){
            throw $this->createAccessDeniedException();
        }

        $selectedPosts = $query->execute();

        try {
            $this->publishOnFacebook($selectedPosts);
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_batch_newsletter_error'), $this->admin->flashFacebookIcon);
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function batchActionNewsletterAndFacebook(ProxyQueryInterface $query)
    {
        if(!$this->admin->isGranted('ROLE_POST_ADMIN')){
            throw $this->createAccessDeniedException();
        }

        $selectedPosts = $query->execute();

        try {
            $this->shareNewsletter($selectedPosts);
            $this->publishOnFacebook($selectedPosts);
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_batch_newsletter_error'), $this->admin->flashNewsletterIcon);
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    private function shareNewsletter($posts)
    {
        $modelManager = $this->admin->getModelManager();
        $newsletterManager = $this->get('bcp.newsletter');

        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $newsletterManager->setIsSuperAdmin(true);
        }

        $shared = $newsletterManager->shareList($posts);

        if($shared['sended']){
            foreach($posts as $post){
                $modelManager->update($post);
            }

            $this->addFlash('sonata_flash_success', $this->admin->trans('flash_batch_newsletter_success'), $this->admin->flashNewsletterIcon);
        } else {
            $message = $this->admin->trans('flash_batch_newsletter_error');
            $message .= '<ul>';

            foreach($shared['errors'] as $error){
                $message .= sprintf('<li>%s</li>', $error);
            }

            $message .= '</ul>';
            $this->addFlash('sonata_flash_error', $message, $this->admin->flashNewsletterIcon);
        }
    }

    private function publishOnFacebook($posts)
    {
        $modelManager = $this->admin->getModelManager();
        $facebookManager = $this->get('bcp.facebook');

        $fbResponses = $facebookManager->publishListOnPage($posts);

        if(empty($fbResponses['errors'])){
            $this->addFlash('sonata_flash_success', $this->admin->trans('flash_batch_facebook_success_all'), $this->admin->flashFacebookIcon);
        } else {
            $postsInError = array();

            $message = $this->admin->trans('flash_batch_facebook_error');
            $message .= '<ul>';

            foreach($fbResponses['errors'] as $error){
                $message .= sprintf('<li>%s: %s</li>', $error['post'], $this->admin->trans($error['message']));
                $postsInError[] = $error['post'];
            }

            $message .= '</ul>';

            $this->addFlash('sonata_flash_error', $message, $this->admin->flashFacebookIcon);

            foreach($posts as $post){
                if(!in_array($post->getTitle(), $postsInError)){
                    $this->addFlash('sonata_flash_success', $this->admin->trans('flash_batch_facebook_success', array(
                        '%post_title%' => $post->getTitle()
                    )), $this->admin->flashFacebookIcon);
                }
            }
        }

        foreach($posts as $post){
            $modelManager->update($post);
        }
    }
}