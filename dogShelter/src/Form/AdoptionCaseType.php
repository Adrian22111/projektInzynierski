<?php

namespace App\Form;

use App\Entity\Dog;
use App\Entity\User;
use App\Entity\AdoptionCase;
use App\Repository\DogRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdoptionCaseType extends AbstractType
{
    // protected $clientid = null;


    // public function __construct (AdoptionCase $adoptionCase)
    // {
    //     $this->clientid = $adoptionCase->getClient()->getId();
    // }
    



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {            
        // $clientid = $this->clientid;
        $builder
            ->add('dog',EntityType::class,[
                'class' => Dog::class,
                'choice_label' => 'name',
                'mapped' => true,
                'multiple' => false,
                'query_builder' => function(DogRepository $dogs) use ($options)
                {
                    $dogId = $options['dogId'];
                    $condition = false;
                    return $dogs->createQueryBuilder('d')
                        ->andWhere('d.inAdoption = :condition')
                        ->setParameter('condition', $condition)
                        ->orWhere('d.id = :id')
                        ->setParameter('id', $dogId)
                        ->orderBy('d.name','ASC')
                        ;
                }
            ])
            ->add('employee',EntityType::class,[
                'class' => User::class,
                'choice_label' => 'username',
                'mapped' => true,
                'multiple' => true,
                'query_builder' => function(UserRepository $users)
                {
                    $condition = true;
                    $role = "ROLE_CLIENT";
                    return $users->createQueryBuilder('u')
                        ->where('u.roles NOT LIKE :role')
                        ->setParameter('role', "%$role%")
                        ->andWhere('u.available = :condition')
                        ->setParameter('condition', $condition)
                        ->orderBy('u.username','ASC')
                        ;

                    // $condition = true;
                    // $role = 'ADMIN';
                    // return $users->createQueryBuilder('u')
                    //     ->andWhere('u.available = :condition')
                    //     ->setParameter('condition', $condition)
                    //     ->andWhere('u.roles LIKE :role')

                }
            ])
            ->add('client',EntityType::class,[
                'class' => User::class,
                'choice_label' => 'username',
                'mapped' => true,
                'multiple' => false,
                'query_builder' => function(UserRepository $users) use ($options)
                {
                    $clientId = $options['clientId'];
                    

                    $condition = true;
                    $role = "ROLE_PRACOWNIK";
                    $admin = "ROLE_ADMIN";
                    return $users->createQueryBuilder('u')
                        ->where('u.roles NOT LIKE :role')
                        ->setParameter('role', "%$role%")
                        ->andWhere('u.roles NOT LIKE :admin')
                        ->setParameter('admin', "%$admin%")
                        ->andWhere('u.available = :condition')
                        ->setParameter('condition', $condition)
                        ->orWhere('u.id = :id')
                        ->setParameter('id', $clientId) // ogarnąć sposób przekazywanie id z kontrolera tutaj
                        ->orderBy('u.username','ASC')              
                        ;
                }
            ])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdoptionCase::class,
            'clientId' => null,
            'dogId' => null,
        ]);
    }
}
