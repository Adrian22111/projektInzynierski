<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ChangePasswordType extends AbstractType
{
    public function __construct(private Security $security)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword',PasswordType::class,[
                'label'=>'Stare Hasło',
                'mapped' => false,
                'required'=> true,
            ])
            ->add('password',PasswordType::class,[
                'mapped' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Hasła muszą być takie same',
                'required' => true,
                'mapped' => false,
                'first_options'  => ['label' => 'Nowe Hasło'],
                'second_options' => ['label' => 'Powtórz Hasło'],

            ]);
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event) use ($options) {
            $form = $event->getForm();
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            $currentUserId = $currentUser->getId();
            $editedUserId = $options['editedUserId'];
            if($this->security->isGranted('ROLE_ADMIN') && $currentUserId!=$editedUserId )
            {
                $form->add('oldPassword',PasswordType::class,[
                    'label'=>'Stare Hasło',
                    'mapped' => false,
                    'disabled' => true,
                    'required'=> false,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => User::class,
            'editedUserId' => null,
        ]);

    }
}
