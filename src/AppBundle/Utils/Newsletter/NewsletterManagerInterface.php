<?php

namespace AppBundle\Utils\Newsletter;

use AppBundle\Entity\Post;

interface NewsletterManagerInterface
{
    /**
     * Share post's informations with all suscribers.
     *
     * @param Post $post
     * @return \DateTime|null
     */
    public function share(Post $post);
}