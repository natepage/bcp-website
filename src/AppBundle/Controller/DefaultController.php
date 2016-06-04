<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Newsletter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Post');

        if($this->isGranted('ROLE_POST_UPDATE')){
            $query = $repo->getFindAllByCreated();
        } else {
            $query = $repo->getFindLatestQuery();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('default/index.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/article/{slug}", options={"expose"=true}, name="front_article_view")
     */
    public function postViewAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Post');

        if(null === $post = $repo->findOneBy(array('slug' => $slug))){
            throw $this->createNotFoundException(sprintf("L'article [%s] n'existe pas.", $slug));
        }

        $detector = $this->get('vipx_bot_detect.detector');
        $botMetadata = $detector->detectFromRequest($request);

        if(null === $botMetadata && $post->getPublished()){
            $post->addViews();
            $em->flush();
        }

        return $this->render(':Post:view.html.twig', array('post' => $post, 'backUrl' => $request->headers->get('referer')));
    }

    /**
     * @Route("/page/listing", name="front_listing")
     */
    public function listingAction()
    {
        $url = "http://www.ffbsq.org/bowling/listing/listing-ws.php?output=xml&asfile=false&num_licence=&departement=&region=&club=poitevin&nom=";

        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents($url));

        $listing = array();

        foreach($crawler as $xListing){
            foreach($xListing->childNodes as $joueur){
                $tmpJoueur = array();

                foreach($joueur->attributes as $attr){
                    $tmpJoueur[$attr->name] = $joueur->getAttribute($attr->name);
                }

                $listing[] = $tmpJoueur;
            }
        }

        $dynamicArray = $this->getDynamicArray($listing);
        array_multisort($dynamicArray['moyenne'], SORT_DESC, $dynamicArray['nom'], SORT_ASC, $listing);

        return $this->render(':default:listing.html.twig', array('listing' => $listing));
    }

    /**
     * @Route("/page/gallery", name="front_gallery")
     */
    public function galleryAction(Request $request)
    {
        if(!$this->isGranted('ROLE_POST_UPDATE')){
            $query = $this->getDoctrine()->getRepository('AppBundle:Image')->getWithArticlePublishedQuery();
        } else {
            $query = $this->getDoctrine()->getRepository('AppBundle:Image')->getAllWithArticleQuery();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            40
        );

        return $this->render(':default:gallery.html.twig', array('images' => $pagination));
    }

    /**
     * @Route("/page/{slug}", name="front_page_view")
     */
    public function pageViewAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Page');

        if(null === $page = $repo->findOneBy(array('slug' => $slug))){
            throw $this->createNotFoundException(sprintf("La page [%s] n'existe pas.", $slug));
        }

        $detector = $this->get('vipx_bot_detect.detector');
        $botMetadata = $detector->detectFromRequest($request);

        if(null === $botMetadata && $page->getPublished()){
            $page->addViews();
            $em->flush();
        }

        return $this->render(':Page:view.html.twig', array('page' => $page));
    }

    /**
     * @Route("/newsletter/register", name="front_newsletter_register")
     */
    public function registerNewsletterAction(Request $request)
    {
        $mail = $request->request->get('bcp-newsletter-mail');
        $emailConstraint = new EmailConstraint();
        $emailConstraint->message = 'Votre adresse mail est invalide...';
        $errors = $this->get('validator')->validate($mail, $emailConstraint);

        $em = $this->getDoctrine()->getManager();
        $newsletterAlreadyExist = $em->getRepository('AppBundle:Newsletter')->findOneBy(array('mail' => $mail));
        $userMailAlreadyExist = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $mail));

        if(null !== $newsletterAlreadyExist || null !== $userMailAlreadyExist){
            $request->getSession()->getFlashBag()->add('danger', 'Cette adresse mail a déjà été ajoutée.');
        } elseif($errors->count() === 0 && $mail != ''){
            $newsletter = new Newsletter();
            $newsletter->setMail($mail);
            $newsletter->setToken($this->get('fos_user.util.token_generator')->generateToken());

            $em->persist($newsletter);
            $em->flush();

            $flash = sprintf('L\'adresse mail "%s" a bien été ajoutée. Vous recevrez dès à présent nos nouveautés.', $mail);
            $request->getSession()->getFlashBag()->add('success', $flash);
        } else {
            $request->getSession()->getFlashBag()->add('danger', $emailConstraint->message);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/newsletter/remove/{token}", name="front_newsletter_remove")
     */
    public function removeNewsletterAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('AppBundle:Newsletter')->findOneBy(array('token' => $token));

        if(null === $newsletter){
            $request->getSession()->getFlashBag()->add('danger', 'Le token de vérification ne correspond pas...');
        } else {
            $em->remove($newsletter);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Vous ne recevrez plus nos nouveautés. Nous espérons tout de même vous revoir bientôt.');
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/ajax/cookie/consent",  name="ajax_cookie_consent")
     */
    public function cookieConsent(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            throw $this->createNotFoundException();
        }

        $cookie = new Cookie('bcp_cookie_consent', 'true', time() + (10 * 365 * 24 * 60 * 60));

        $response = new Response("ok");
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Route("/archives/archive_bcp_{years}", name="archives")
     */
    public function archivesAction($years)
    {
        $template = 'default/archives/bcp_' . $years . '.html.twig';

        if(!$this->get('templating')->exists($template)){
            throw $this->createNotFoundException();
        }

        return $this->render($template);
    }

    /**
     * @Route("/recherche", name="recherche")
     */
    public function searchAction()
    {
        return $this->render(':Page:search.html.twig');
    }

    private function getDynamicArray($array) {
        $retour = array();

        foreach ($array as $row) {
            foreach ($row as $k => $v) {
                $retour[$k][] = $v;
            }//foreach
        }//foreach

        return $retour;
    }//getDynamicArray

    /**
     * Fonction qui gère les mauvaises URL
     *
     * @param $url
     */
    public function wrongRouteAction($url){
        throw $this->createNotFoundException();
    }
}
