<?php

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;

class EmailController extends CRUDController
{
    public function batchActionSendEmail(ProxyQueryInterface $query)
    {
        if(!$this->admin->isGranted('ROLE_EMAIL_ADMIN')){
            throw $this->createAccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        $emailSender = $this->get('bcp.email_sender');
        $contactProvider = $this->get('bcp.contact_provider');
        $templating = $this->get('templating');
        $from = $this->getParameter('newsletter_from');
        $selectedEmails = $query->execute();
        $errors = array();

        try {
            foreach($selectedEmails as $email){
                if(null === $email->getEmailFrom()){
                    $email->setEmailFrom($from);
                } else {
                    $from = $email->getEmailFrom();
                }

                $emails = in_array('all', $email->getContacts()) ? $contactProvider->getContactsEmail() : $email->getContacts();
                $subject = sprintf('[BCP] %s', $email->getSubject());
                $body = $templating->render('@App/Utils/email_structure.html.twig', array('body' => $email->getBody()));
                
                if(!empty($emails)){
                    foreach($emails as $to){
                        $sent = $emailSender->send($from, array($to), $subject, $body);

                        if(!$sent){
                            $errors[] = sprintf('%s - %s', $subject, $to);
                        }
                    }

                    $email->setSent(new \Datetime());
                    $modelManager->update($email);
                } else {
                    $errors[] = sprintf('%s - %s', $subject, $this->admin->trans('email_to_empty'));
                }
            }

            if(!empty($errors)){
                $error = $this->admin->trans('flash_batch_send_email_error');
                $error .= '<ul>';

                foreach($errors as $err){
                    $error .= sprintf('<li>%s</li>', $err);
                }

                $error .= '</ul>';

                $this->addFlash('sonata_flash_error', $error, $this->admin->flashIcon);
            } else {
                $this->addFlash('sonata_flash_success', $this->admin->trans('flash_batch_send_email_success'), $this->admin->flashIcon);
            }
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', $this->admin->trans('flash_batch_send_email_error'), $this->admin->flashIcon);
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}