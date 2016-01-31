<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    private $roles;

    /**
     * Constructor
     */
    public function __construct($roles)
    {
        $this->roles = $this->setRoles($roles);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'Identifiant',
                'attr' => array(
                    'class' => 'form-control'
                )//attr
            ))//username

            ->add('email', 'email', array(
                'label' => 'Email',
                'attr' => array(
                    'class' => 'form-control'
                )//attr
            ))//email

            ->add('licence', 'text', array(
                'label' => 'N° de licence',
                'attr' => array(
                    'class' => 'form-control'
                ),//attr
                'required' => false
            ))//licence

            ->add('plain_password', 'password', array(
                'label' => 'Mot de passe',
                'attr' => array(
                    'class' => 'form-control'
                ),//attr
                'required' => false
            ))//plain_password

            ->add('roles', 'choice', array(
                'label' => 'Roles',
                'attr' => array(
                    'class' => 'form-control',
                    'size' => $this->getRolesRows()
                ),//attr
                'choices' => $this->roles,
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ))//roles

            ->add('enabled', 'checkbox', array(
                'label' => 'Activé(e)',
                'attr' => array(
                    'style' => 'margin-left: 10px;',
                ),//attr
                'required' => false
            ))//enabled

            ->add('locked', 'checkbox', array(
                'label' => 'Bloqué(e)',
                'attr' => array(
                    'style' => 'margin-left: 10px;',
                ),//attr
                'required' => false
            ))//enabled
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_user';
    }

    private function setRoles($hierarchy = array()){
        $roles = array();
        $adminRole = 'ROLE_ADMIN';

        if(isset($hierarchy[$adminRole])){
            $roles[$adminRole] = $this->getRoleLayout($adminRole);

            foreach($hierarchy[$adminRole] as $all){
                if(isset($hierarchy[$all])){
                    $tmp = array();
                    foreach($hierarchy[$all] as $role){
                        $tmp[$role] = $this->getRoleLayout($role);
                    }

                    $roles[$this->getRoleEntity($all)] = $tmp;
                }
            }
        }

        return $roles;
    }

    private function getRoleEntity($role)
    {
        $retour = explode('_', $role);
        return $retour[1];
    }

    private function getRoleLayout($role)
    {
        $name = '';
        $roleName = explode('_', $role);
        array_shift($roleName);
        foreach ($roleName as $part) {
            $name .= $part . ' ';
        }

        return $name;
    }

    private function getRolesRows()
    {
        $cpt = count($this->roles);
        foreach($this->roles as $row){
            if(is_array($row)){
                $cpt += count($row);
            }
        }

        return $cpt;
    }
}
