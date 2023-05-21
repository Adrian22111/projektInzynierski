<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\User;
use App\Entity\Status;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class changeGuardianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('guardian',EntityType::class,[
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getName() . ' ' . $user->getSurname();
                },
                'mapped' => true,
                'multiple' => true,
                'required' => true,
                'query_builder' => function(UserRepository $users)
                {
                    $condition = true;
                    $admin = "ROLE_ADMIN";
                    $employee = "ROLE_PRACOWNIK";
                   return $users->createQueryBuilder('u')
                    ->andWhere('u.archived = :archived')
                    ->setParameter('archived',false)
                    ->andWhere('u.roles LIKE :admin')
                    ->setParameter('admin',"%$admin%") 
                    ->orWhere('u.roles LIKE :employee')
                    ->setParameter('employee',"%$employee%")
                    ->andWhere('u.available = :condition')
                    ->setParameter('condition', $condition)
                    ->andWhere('u.archived = :archived')
                    ->setParameter('archived',false)
                    ->orderBy('u.username','ASC'); 
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dog::class,
        ]);
    }
}