<?php

namespace AppBundle\Events;

use AppBundle\Entity\SharedFile;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class SubmittedSharedFileEvent extends Event
{
    /**
     * @var SharedFile
     */
    private $sharedFile;

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(SharedFile $sharedFile, UserInterface $user)
    {
        $this->sharedFile = $sharedFile;
        $this->user = $user;
    }

    /**
     * Get sharedFile
     *
     * @return SharedFile
     */
    public function getSharedFile()
    {
        return $this->sharedFile;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}