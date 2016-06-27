<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PageController extends CRUDController
{
    public function preCreate(Request $request, $page)
    {
        $page->setAuthor($this->getUser());
    }
}