<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_homepage")
     */
    public function indexAction()
    {
        if($this->isGranted('ROLE_PAGE_SEE')){
            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'page')));
        } elseif($this->isGranted('ROLE_POST_SEE')){
            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'post')));
        } elseif($this->isGranted('ROLE_USER_SEE')){
            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'user')));
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/{entity}", name="admin_entity_list")
     */
    public function entityListAction(Request $request, $entity)
    {
        $class = $this->getClassName($entity);
        $repo = $this->getDoctrine()->getRepository('AppBundle:'.$class);
        $search = $request->query->get('search');

        if(null !== $search && $search != ''){
            $query = $repo->getFindSearchQuery($search);
        } elseif(null === $search) {
            $query = $repo->getFindAllQuery();
        } else {
            return $this->redirect($this->generateUrl($request->attributes->get('_route'), $request->attributes->get('_route_params')));
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
            $repo->getDefaultPaginationField()
        );

        return $this->render('@App/Admin/'.$class.'/list.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/{entity}/see/{id}", name="admin_entity_see", requirements={"id": "\d+"})
     */
    public function entitySeeAction($entity, $id)
    {
        $class = $this->getClassName($entity);
        $repo = $this->getDoctrine()->getRepository('AppBundle:'.$class);

        if(null === $entityObject = $repo->find($id)){
            throw $this->createNotFoundException(sprintf("%s à l'id %s n'existe pas.", $class, $id));
        }

        return $this->render('@App/Admin/'.$class.'/see.html.twig', array('entity' => $entityObject));
    }

    private function getClassName($entity)
    {
        return ucfirst($entity);
    }

    /**
     * @Route("/wash/newsletters", name="admin_wash_newsletters")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function washNewslettersAction()
    {
        $washedList = array();
        $em = $this->getDoctrine()->getManager();
        $newsletters = $em->getRepository('AppBundle:Newsletter')->findAll();

        foreach($newsletters as $newsletter){
            if(!in_array($newsletter->getMail(), $washedList)){
                array_push($washedList, $newsletter->getMail());
            } else {
                $em->remove($newsletter);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'newsletter')));
    }
}
