<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('email',EmailType::class)
            ->add('phoneNumber',TelType::class,[
                'required' => false,
                
            ])
            ->add('profileImage',FileType::class,[
                'label' => 'Zdjęcie profilowe jpg/png',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Dostępne formaty to PNG/JPG'
                    ])
                ]
            ]);

        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
            $form = $event->getForm();
            $user = $event->getData();
            if(!$user || null === $user->getId())
            {
                $form->add('password',PasswordType::class)
                     
                ;
                
                
            }
            else
            {
                $roles = $user->getRoles();
                foreach($roles as $role)
                {
                    if($role =="ROLE_PRACOWNIK" || $role == "ROLE_ADMIN")
                    {
                        $form
                            ->add('description',TextareaType::class,[
                                'required' => false,
                            ])
                            ->add('facebookProfile',UrlType::class,[
                                'required' => false,
                            ])
                            ->add('guardianOf')
                            ->add('available');

                
                    }
                }
                                    
            }

        });
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
