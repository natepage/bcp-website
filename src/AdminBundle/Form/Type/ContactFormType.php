<?php

namespace AdminBundle\Form\Type;

use AppBundle\Utils\Newsletter\ContactProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    /**
     * @var ContactProviderInterface
     */
    private $contactProvider;

    public function __construct(ContactProviderInterface $contactProvider)
    {
        $this->contactProvider = $contactProvider;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getContacts(),
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'preferred_choices' => array('all'),
            'translation_domain' => 'EmailAdmin'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_contacts';
    }

    private function getContacts()
    {
        return array_merge($this->contactProvider->getContactsFormRepresentation(), array(
            'all' => 'form.select_all_contacts'
        ));
    }
}