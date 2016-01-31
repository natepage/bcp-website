<?php

namespace AppBundle\EventListeners;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Templating\EngineInterface;

class KernelRequestListener
{
    /**
     * @var string
     */
    private $cookieName = 'bcp_cookie_consent';

    /**
     * @var string
     */
    private $cookieTemplate = 'AppBundle:Utils:cookie_consent.html.twig';

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function handleCookieConsent(FilterResponseEvent $event)
    {
        if(!$event->isMasterRequest() || $event->getRequest()->cookies->has($this->cookieName)){
            return;
        }

        $response = $event->getResponse();
        $response->setContent($response->getContent() . $this->templating->render($this->cookieTemplate));
    }
}