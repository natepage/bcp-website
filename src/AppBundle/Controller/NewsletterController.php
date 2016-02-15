<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Newsletter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin/newsletter")
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/remove/{id}", name="admin_newsletter_remove", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_NEWSLETTER_REMOVE')")
     */
    public function removeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $newsletter = $em->getRepository('AppBundle:Newsletter')->find($id)){
            throw $this->createNotFoundException(sprintf("L'adresse de newsletter à l'id %s n'existe pas.", $id));
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            $em->remove($newsletter);
            $em->flush();

            $flash = sprintf("L'adresse de newsletter \"%s\"a bien été supprimée.", $newsletter->getMail());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'newsletter')));
        }

        return $this->render('@App/Admin/Newsletter/remove.html.twig', array('form' => $form->createView(), 'newsletter' => $newsletter));
    }
}
