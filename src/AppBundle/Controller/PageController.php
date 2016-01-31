<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use AppBundle\Form\PageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/page")
 */
class PageController extends Controller
{
    /**
     * @Route("/create", name="admin_page_create")
     * @Security("has_role('ROLE_PAGE_CREATE')")
     */
    public function createAction(Request $request)
    {
        $page = new Page();
        $page->setAuthor($this->getUser());
        $form = $this->createForm(new PageType(), $page);

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $page->setSlug($this->get('slugify')->slugify($page->getTitle()));
            $em->persist($page);
            $em->flush();

            $flash = sprintf("La page \"%s\"a bien été créée.", $page->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'page')));
        }

        return $this->render('@App/Admin/Page/create.html.twig', array('form' => $form->createView(), 'page' => $page));
    }

    /**
     * @Route("/update/{id}", name="admin_page_update", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_PAGE_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $page = $em->getRepository('AppBundle:Page')->find($id)){
            throw $this->createNotFoundException(sprintf("La page à l'id %s n'existe pas.", $id));
        }

        $form = $this->createForm(new PageType(), $page);

        if($form->handleRequest($request)->isValid()){
            $page->setSlug($this->get('slugify')->slugify($page->getTitle()));
            $em->flush();

            $flash = sprintf("La page \"%s\"a bien été modifiée.", $page->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'page')));
        }

        return $this->render('@App/Admin/Page/update.html.twig', array('form' => $form->createView(), 'page' => $page));
    }

    /**
     * @Route("/remove/{id}", name="admin_page_remove", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_PAGE_REMOVE')")
     */
    public function removeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $page = $em->getRepository('AppBundle:Page')->find($id)){
            throw $this->createNotFoundException(sprintf("La page à l'id %s n'existe pas.", $id));
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            $em->remove($page);
            $em->flush();

            $flash = sprintf("La page \"%s\"a bien été supprimée.", $page->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'page')));
        }

        return $this->render('@App/Admin/Page/remove.html.twig', array('form' => $form->createView(), 'page' => $page));
    }
}
