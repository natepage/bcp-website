<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 28/08/2015
 * Time: 16:19
 */

namespace AppBundle\Utils;

use AppBundle\Entity\Post;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class NewsletterManager
{
    private $om;
    private $templating;
    private $from;

    /**
     * Constructor
     */
    public function __construct(ObjectManager $om, EngineInterface $templating, $from)
    {
        $this->om = $om;
        $this->templating = $templating;
        $this->from = $from;
    }

    public function share(Post $post)
    {
        if(!$post->getPublished()){
            return null;
        }

        $users = $this->getUsers();
        $newsletters = $this->getNewsletters();

        $now = new \DateTime();
        $subject = sprintf("[%s] Des nouveautés sur le site du BCP", $now->format('d-m-Y'));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from);

        $transport = \Swift_MailTransport::newInstance();
        $mailer = \Swift_Mailer::newInstance($transport);

        foreach($users as $user){
            $template = $this->templating->render('@App/Admin/Post/newsletter.html.twig', array(
                'post' => $post,
                'user' => $user
            ));

            $message->setTo(array($user->getEmail()));
            $message->setBody($template, 'text/html', 'utf-8');

            if(!$mailer->send($message)){
                return null;
            }
        }

        foreach($newsletters as $newsletter){
            $template = $this->templating->render('@App/Admin/Post/newsletter.html.twig', array(
                'post' => $post,
                'newsletter' => $newsletter
            ));

            $message->setTo(array($newsletter->getMail()));
            $message->setBody($template, 'text/html', 'utf-8');

            if(!$mailer->send($message)){
                return null;
            }
        }

        return $now;
    }

    private function getUsers()
    {
        return $this->om
                    ->getRepository('AppBundle:User')
                    ->findBy(array('newsletter' => 1));
    }

    private function getNewsletters()
    {
        return $this->om
                    ->getRepository('AppBundle:Newsletter')
                    ->findAll();
    }
}