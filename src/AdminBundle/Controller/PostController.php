<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class PostController extends CRUDController
{
    public function preCreate(Request $request, $post)
    {
        $post->setAuthor($this->getUser());
    }

    public function preEdit(Request $request, $post)
    {
        $this->admin->handleOldElements($post, array('images', 'pdfs'));
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $this->admin->checkAccess('batchDelete');

        $liipManager = $this->get('liip_imagine.cache.manager');
        $modelManager = $this->admin->getModelManager();
        $selectedPosts = $query->execute();

        try {
            foreach($selectedPosts as $post){
                foreach($post->getImages() as $image){
                    $liipManager->remove($image->getWebPath());
                }

                $modelManager->delete($post);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }
}