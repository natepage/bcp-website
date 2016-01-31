<?php
/**
 * Created by PhpStorm.
 * User: nathanpage
 * Date: 30/08/2015
 * Time: 17:15
 */

namespace AppBundle\Utils;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TwigSitePagesExtension extends \Twig_Extension
{
    private $om;
    private $ac;
    private $ts;

    /**
     * Constructor
     */
    public function __construct(ObjectManager $om, AuthorizationCheckerInterface $ac, TokenStorageInterface $ts)
    {
        $this->om = $om;
        $this->ac = $ac;
        $this->ts = $ts;
    }

    public function getGlobals()
    {
        return array(
            'sitePages' => $this->getPages()
        );
    }

    public function getName()
    {
        return 'bcp_sitepages';
    }

    private function getPages()
    {
        if(null !== $this->ts->getToken()){
            if($this->ac->isGranted('ROLE_PAGE_UPDATE')){
                return $this->om->getRepository('AppBundle:Page')->findAllOrderByPriority();
            }
        }

        return $this->om->getRepository('AppBundle:Page')->findAllOrderByPriorityAndPublished();
    }
}