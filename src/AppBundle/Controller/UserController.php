<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/create", name="admin_user_create")
     * @Security("has_role('ROLE_USER_CREATE')")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $userType = $this->get('bcp.usertype');
        $form = $this->createForm($userType, $user);

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $flash = sprintf("L'utilisateur \"%s\"a bien été créé.", $user->getUsername());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'user')));
        }

        return $this->render('@App/Admin/User/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/update/{id}", name="admin_user_update", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $user = $em->getRepository('AppBundle:User')->findFully($id)){
            throw $this->createNotFoundException(sprintf("L'utilisateur à l'id %s n'existe pas.", $id));
        }

        $userType = $this->get('bcp.usertype');
        $form = $this->createForm($userType, $user);

        $oldUsername = $user->getUsername();

        if($form->handleRequest($request)->isValid()){
            if($oldUsername !== $user->getUsername()){
                foreach($user->getPosts() as $post){
                    $post->setAuthorName($user->getUsername());
                }

                foreach($user->getPages() as $page){
                    $page->setAuthorName($user->getUsername());
                }
            }

            $em->flush();

            $flash = sprintf("L'utilisateur \"%s\"a bien été modifié.", $user->getUsername());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'user')));
        }

        return $this->render('@App/Admin/User/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/remove/{id}", name="admin_user_remove", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER_REMOVE')")
     */
    public function removeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $user = $em->getRepository('AppBundle:User')->find($id)){
            throw $this->createNotFoundException(sprintf("L'utilisateur à l'id %s n'existe pas.", $id));
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            foreach($user->getPosts() as $post){
                $post->setAuthor(null);
            }

            $em->remove($user);
            $em->flush();

            $flash = sprintf("L'utilisateur \"%s\"a bien été supprimé.", $user->getUsername());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'user')));
        }

        return $this->render('@App/Admin/User/remove.html.twig', array('form' => $form->createView(), 'user' => $user));
    }

    /**
     * @Route("/email/{id}", name="admin_user_email", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER_EMAIL')")
     */
    public function emailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $user = $em->getRepository('AppBundle:User')->find($id)){
            throw $this->createNotFoundException(sprintf("L'utilisateur à l'id %s n'existe pas.", $id));
        }

        $form = $this->createForm(new EmailType(), array(
            'from' => 'nathan.page86@gmail.com',
            'to' => $user->getEmail()
        ));

        if($form->handleRequest($request)->isValid()){
            $email = $form->getData();

            $templating = $this->get('templating');
            $template = $templating->render('@App/Utils/email_structure.html.twig', array('body' => $email['body']));

            $message = \Swift_Message::newInstance()
                ->setSubject($email['subject'])
                ->setFrom($email['from'])
                ->setTo(explode(',', $email['to']))
                ->setBody($template, 'text/html', 'utf-8');

            $transport = \Swift_MailTransport::newInstance();
            $mailer = \Swift_Mailer::newInstance($transport);
            $mailer->send($message);

            $flash = sprintf("Le mail a bien été envoyé à l'utilisateur \"%s\".", $user->getUsername());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'user')));
        }

        return $this->render('@App/Admin/User/email.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    public function bowlingInfosAction($num_licence)
    {
        $url = $this->get('bcp.external_url_generator');
        $url->setUrl("http://www.ffbsq.org/bowling/listing/listing-ws.php");
        $url->addArguments(array(
            'ouput' => 'xml',
            'asfile' => 'false',
            'num_licence' => $num_licence
        ));

        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents($url->generate()));
        $infos = $crawler->filter('listing > joueur');

        return $this->render(':User:bowling_infos.html.twig', array('infos' => ($infos->count() == 0) ? null : $infos->first()));
    }
}
