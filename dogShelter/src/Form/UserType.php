<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles',ChoiceType::class,array(
                'choices' => array(
                    'Uprawnienia' =>array(
                        'admin'=>'ROLE_ADMIN',
                        'pracownik'=>'ROLE_PRACOWNIK',
                        'klient' =>'ROLE_CLIENT'
                    )
                    ),
                    'multiple'=>true,
                    'required'=>true
            ))
            ->add('available')
            ->add('password',PasswordType::class)
            ->add('email',EmailType::class)
            ->add('description',TextareaType::class)
            ->add('facebookProfile',UrlType::class)
            ->add('phoneNumber',TelType::class)
            ->add('profileImage')
            ->add('guardianOf')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
