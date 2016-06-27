<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;

class CRUDController extends BaseCRUDController
{
    protected function addFlash($type, $message, $flashIcon = null)
    {
        $icon = $flashIcon !== null ? $flashIcon : $this->admin->flashIcon;

        if(null !== $icon){
            $flash = '<div class="admin-flash-icon">%s</div><div class="admin-flash-content">%s</div>';

            $message = sprintf($flash, $icon, $message);
        }

        parent::addFlash($type, $message);
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $this->admin->checkAccess('batchDelete');

        $modelManager = $this->admin->getModelManager();
        try {
            $modelManager->batchDelete($this->admin->getClass(), $query);
            $this->addFlash('sonata_flash_success', $this->admin->trans('flash_batch_delete_success', array(), 'SonataAdminBundle'));
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_batch_delete_error', array(), 'SonataAdminBundle'));
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}