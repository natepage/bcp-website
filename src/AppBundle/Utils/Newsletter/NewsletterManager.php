<?php

namespace AppBundle\Utils\Newsletter;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class NewsletterManager implements NewsletterManagerInterface
{
    /**
     * @var ContactProviderInterface
     */
    private $contactProvider;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $from;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor
     */
    public function __construct(
        ContactProviderInterface $contactProvider,
        EngineInterface $templating,
        $from,
        TranslatorInterface $translator
    )
    {
        $this->contactProvider = $contactProvider;
        $this->templating = $templating;
        $this->from = $from;
        $this->translator = $translator;
    }

    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->contactProvider->setIsSuperAdmin($isSuperAdmin);
    }

    /**
     * {@inheritdoc}
     */
    public function share(Post $post)
    {
        if(!$post->getPublished()){
            return null;
        }

        $contacts = $this->contactProvider->getContacts();

        $now = new \DateTime();

        $message = $this->getMessage()
                        ->setSubject($this->getSubject($now))
                        ->setFrom($this->from);

        $mailer = $this->getMailer();
        
        foreach($contacts as $contact){
            if(!$contact instanceof ContactInterface){
                throw new \InvalidArgumentException(sprintf("%s must implement %s.", get_class($contact), ContactInterface::class));
            }

            $template = $this->templating->render('@App/Admin/Post/newsletter.html.twig', array(
                'post' => $post,
                'contact' => $contact
            ));

            $message->setTo(array($contact->getEmail()));
            $message->setBody($template, 'text/html', 'utf-8');

            if($mailer->send($message)){
                $post->setSharedNewsletter($now);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shareList(array $posts)
    {
        $contacts = $this->contactProvider->getContacts();
        $now = new \DateTime();
        $message = $this->getMessage()->setSubject($this->getSubject($now))->setFrom($this->from);
        $mailer = $this->getMailer();

        $response = array(
            'sended' => false,
            'errors' => array()
        );

        foreach($posts as $key => $post){
            if(!$post->getPublished()){
                $response['errors'][] = $this->translator->trans('newsletter_post_not_published', array(
                    '%post_title%' => $post->getTitle()
                ), 'PostAdmin');
            }
        }

        if(empty($response['errors'])){
            foreach($contacts as $contact){
                if(!$contact instanceof ContactInterface){
                    throw new \InvalidArgumentException(sprintf("%s must implement %s.", get_class($contact), ContactInterface::class));
                }

                $template = $this->templating->render('@Admin/Batch/Newsletter/share_posts.html.twig', array(
                    'posts' => $posts,
                    'contact' => $contact
                ));

                $message->setTo(array($contact->getEmail()));
                $message->setBody($template, 'text/html', 'utf-8');

                if($mailer->send($message)){
                    foreach($posts as $post){
                        $post->setSharedNewsletter($now);
                    }

                    $response['sended'] = true;
                }
            }
        }

        return $response;
    }

    private function getSubject(\DateTime $date)
    {
        return sprintf("[%s] Des nouveautés sur le site du BCP", $date->format('d-m-Y'));
    }

    private function getMessage()
    {
        return \Swift_Message::newInstance();
    }

    private function getMailer()
    {
        $transport = \Swift_MailTransport::newInstance();

        return \Swift_Mailer::newInstance($transport);
    }
}