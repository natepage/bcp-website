<?php

namespace AppBundle\Utils\Newsletter;

use AppBundle\Entity\Post;

interface NewsletterManagerInterface
{
    /**
     * Share post's informations with all suscribers.
     *
     * @param Post $post
     */
    public function share(Post $post);

    /**
     * Share posts list with all suscribers.
     *
     * @param array $posts
     * @return boolean
     */
    public function shareList(array $posts);
}