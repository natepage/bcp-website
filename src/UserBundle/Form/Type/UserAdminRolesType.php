<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Security\RolesListBuilderInterface;

class UserAdminRolesType extends AbstractType
{
    /**
     * @var RolesListBuilderInterface
     */
    private $rolesListBuilder;

    public function __construct(RolesListBuilderInterface $rolesListBuilder)
    {
        $this->rolesListBuilder = $rolesListBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->rolesListBuilder->buildRolesList(),
            'required' => false,
            'multiple' => true,
            'label' => false,
            'translation_domain' => 'UserAdminRoles'
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
        return 'user_admin_roles';
    }
}