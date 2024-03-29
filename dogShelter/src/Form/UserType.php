<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function __construct(private Security $security)
    {
        
    }
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
            ->add('phoneNumber',TextType::class,[
                'required' => false,

                
            ])
            ->add('profileImage',HiddenType::class)
            ->add('name',TextType::class,[
                'required'=>true,
                'disabled'=>true,
            ])
            ->add('surname',TextType::class,[
                'required' => true,
                'disabled'=>true,
            ]) 


        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
            $form = $event->getForm();
            $user = $event->getData();
            if(!$user || null === $user->getId()) //if new user is created
            {
                $form->add('password',PasswordType::class)
                    ->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Hasła muszą być takie same',
                        'required' => true,
                        'first_options'  => ['label' => 'Hasło'],
                        'second_options' => ['label' => 'Powtórz Hasło'],
                    ]) 
                    ->add('name',TextType::class,[
                        'required'=>true,
                        'disabled'=>false,
                        'empty_data' => '',
                    ])
                    ->add('surname',TextType::class,[
                        'required' => true,
                        'disabled'=>false,
                        'empty_data' => '',
                    ])
              
                ;
                
                
            }
            else //if user is edited
            {
                $roles = $user->getRoles();
                foreach($roles as $role)
                {
                    if($role =="ROLE_PRACOWNIK" || $role == 'ROLE_ADMIN')
                    {
                        $form
                            ->add('username',TextType::class,[
                                'disabled' => true,
                            ])
                            ->add('description',TextareaType::class,[
                                'required' => false,
                            ])
                            ->add('facebookProfile',UrlType::class,[
                                'required' => false,
                            ])
                            ->add('guardianOf')
                            ->add('roles',ChoiceType::class,array(
                                'choices' => array(
                                    'Uprawnienia' =>array(
                                        'admin'=>'ROLE_ADMIN',
                                        'pracownik'=>'ROLE_PRACOWNIK',
                                        'klient' =>'ROLE_CLIENT'
                                    )
                                    ),
                                    'multiple'=>true,
                                    'required'=>true,
                                    'disabled'=>true
                            ))
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
                            if($this->security->isGranted('ROLE_ADMIN'))
                            {
                                $form
                                ->add('username',TextType::class,[
                                    'disabled' => false,
                                ])
                                ->add('available',ChoiceType::class,[
                                    'label' => 'Dostępność',
                                    'mapped' => true,
                                    'required' => true,
                                    'choices' => [
                                        'Dostępny/a' => 1,
                                        'Niedostępny/a' => 0,
                                    ],
                    
                                ])
                                ->add('roles',ChoiceType::class,array(
                                    'choices' => array(
                                        'Uprawnienia' =>array(
                                            'admin'=>'ROLE_ADMIN',
                                            'pracownik'=>'ROLE_PRACOWNIK',
                                            'klient' =>'ROLE_CLIENT'
                                        )
                                        ),
                                        'multiple'=>true,
                                        'required'=>true,
                                        'disabled'=>false,
                                ))
                                ->add('name',TextType::class,[
                                    'required'=>true,
                                    'disabled'=>false,
                                    'empty_data' => '',
                                ])
                                ->add('surname',TextType::class,[
                                    'required' => true,
                                    'disabled'=>false,
                                    'empty_data' => '',
                                    ])

                                ;
                            }

                    }
                    elseif($role =="ROLE_CLIENT")
                    {
                        if($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_PRACOWNIK'))
                        {
                            $form
                            ->add('name',TextType::class,[
                                'required'=>true,
                                'disabled'=>false,
                                'empty_data' => '',
                            ])
                            ->add('surname',TextType::class,[
                                'required' => true,
                                'disabled'=>false,
                                'empty_data' => '',
                            ])
                            ;
                        }
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
