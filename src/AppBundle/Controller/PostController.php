<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/create", name="admin_post_create")
     * @Security("has_role('ROLE_POST_CREATE')")
     */
    public function createAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);
        $fb = $this->get('bcp.facebook');
        $fbToken = $fb->getUserLongAccessToken()->getAccessToken();
        $fbError = false;
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()->getFbAccessToken() !== (string) $fbToken){
            $this->getUser()->setFbAccessToken((string) $fbToken);
            $em->persist($this->getUser());
            $em->flush();
        }

        if($form->handleRequest($request)->isValid()){
            $post->setSlug($this->get('slugify')->slugify($post->getTitle()));
            $post->setAuthor($this->getUser());

            $em->persist($post);
            $em->flush();

            if($form->get('newsletter')->getData() && $post->getPublished()){
                $sharedNewsletter = $this->get('bcp.newsletter')->share($post);
                $post->setSharedNewsletter($sharedNewsletter);

                if(null !== $sharedNewsletter){
                    $newsFlash = 'La newsletter a bien été diffusée.';
                    $request->getSession()->getFlashBag()->add('success', $newsFlash);
                }
            }

            if($form->get('facebook')->getData() && $post->getPublished()){
                $fbPostAsPage = $fb->publishOnPage($post, $form->get('facebook_message')->getData());

                if(!$fbPostAsPage->hasError()){
                    $post->setFbId($fbPostAsPage->getId());

                    $fbFlash = sprintf("L'article \"%s\"a bien été créé sur Facebook.", $post->getTitle());
                    $request->getSession()->getFlashBag()->add('success', $fbFlash);
                } else {
                    $form->addError(new FormError($fbPostAsPage->getException()->getMessage()));
                    $fbError = true;
                }
            }

            $em->persist($post);
            $em->flush();

            $flash = sprintf("L'article \"%s\"a bien été créé.", $post->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            if(!$fbError){
                return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'post')));
            }
        }

        return $this->render('@App/Admin/Post/create.html.twig', array(
            'form' => $form->createView(),
            'post' => $post,
            'fb' => $fb
        ));
    }

    /**
     * @Route("/update/{id}", name="admin_post_update", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_POST_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $post = $em->getRepository('AppBundle:Post')->find($id)){
            throw $this->createNotFoundException(sprintf("L'article à l'id %s n'existe pas.", $id));
        }

        $fb = $this->get('bcp.facebook');
        $fbToken = $fb->getUserLongAccessToken()->getAccessToken();
        $fbError = false;

        if($this->getUser()->getFbAccessToken() !== (string) $fbToken){
            $this->getUser()->setFbAccessToken((string) $fbToken);
            $em->persist($this->getUser());
            $em->flush();
        }

        //Stockage des images actuelles
        $oldImages = new ArrayCollection();

        foreach($post->getImages() as $image){
            $oldImages->add($image);
        }

        //Stockage des pdfs actuels
        $oldPdfs = new ArrayCollection();

        foreach($post->getPdfs() as $pdf){
            $oldPdfs->add($pdf);
        }

        $form = $this->createForm(new PostType(), $post);

        if($form->handleRequest($request)->isValid()){
            $post->setSlug($this->get('slugify')->slugify($post->getTitle()));

            //Suppression des images enlevées du formulaire
            foreach($oldImages as $image){
                if($post->getImages()->contains($image) == false){
                    $this->removeImageCachedFilters($image->getWebPath());
                    $em->remove($image);
                }
            }

            foreach($oldPdfs as $pdf){
                if($post->getPdfs()->contains($pdf) == false){
                    $em->remove($pdf);
                }
            }

            if($form->get('newsletter')->getData() && $post->getPublished()){
                $sharedNewsletter = $this->get('bcp.newsletter')->share($post);
                $post->setSharedNewsletter($sharedNewsletter);

                if(null !== $sharedNewsletter){
                    $newsFlash = 'La newsletter a bien été diffusée.';
                    $request->getSession()->getFlashBag()->add('success', $newsFlash);
                }
            }

            if($form->get('facebook')->getData() && $post->getPublished()){
                $fbPostAsPage = $fb->publishOnPage($post, $form->get('facebook_message')->getData());

                if(!$fbPostAsPage->hasError()){
                    $post->setFbId($fbPostAsPage->getId());

                    $fbFlash = sprintf("L'article \"%s\"a bien été modifié sur Facebook.", $post->getTitle());
                    $request->getSession()->getFlashBag()->add('success', $fbFlash);
                } else {
                    $form->addError(new FormError($fbPostAsPage->getException()->getMessage()));
                    $fbError = true;
                }
            }

            $em->flush();

            $flash = sprintf("L'article \"%s\"a bien été modifié.", $post->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            if(!$fbError){
                return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'post')));
            }
        }

        return $this->render('@App/Admin/Post/update.html.twig', array(
            'form' => $form->createView(),
            'post' => $post,
            'fb' => $fb
        ));
    }

    /**
     * @Route("/remove/{id}", name="admin_post_remove", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_POST_REMOVE')")
     */
    public function removeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $post = $em->getRepository('AppBundle:Post')->find($id)){
            throw $this->createNotFoundException(sprintf("L'article à l'id %s n'existe pas.", $id));
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            foreach($post->getImages() as $image){
                $this->removeImageCachedFilters($image->getWebPath());
            }

            $em->remove($post);
            $em->flush();

            $flash = sprintf("L'article \"%s\"a bien été supprimé.", $post->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'post')));
        }

        return $this->render('@App/Admin/Post/remove.html.twig', array('form' => $form->createView(), 'post' => $post));
    }

    /**
     * @Route("/image/remove/{id}", name="admin_post_image_remove", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_POST_REMOVE')")
     */
    public function imageRemoveAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $post = $em->getRepository('AppBundle:Post')->findFully($id)){
            throw $this->createNotFoundException(sprintf("L'article à l'id %s n'existe pas.", $id));
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            $image = $post->getImage();
            $post->setImage();
            $em->flush();

            $this->removeImageCachedFilters($image->getWebPath());
            $em->remove($image);
            $em->flush();

            $flash = sprintf("L'image de l'article \"%s\"a bien été supprimé.", $post->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'post')));
        }

        return $this->render('@App/Admin/Post/image-remove.html.twig', array('form' => $form->createView(), 'post' => $post));
    }

    /**
     * Remove from cache the image's filters
     *
     * @param string $path
     */
    protected function removeImageCachedFilters($path)
    {
        $this->get('liip_imagine.cache.manager')->remove($path);
    }
}
