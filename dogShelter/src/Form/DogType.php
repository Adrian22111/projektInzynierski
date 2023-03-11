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

class DogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('age')
            ->add('race')
            ->add('sex',ChoiceType::class,[
                'label' => 'Płeć',
                'mapped' => true,
                'required' => true,
                'choices' => [
                    'Samiec' => 'Samiec',
                    'Samica' => 'Samica',
                ],

            ])
            ->add('description',TextareaType::class,[
                'label' => 'Opis',
                'mapped' => true,
                'required' => false,
            ])
            ->add('image',FileType::class,[
                'label' => 'Dodaj Zdjęcie w formacie IMG/JPG',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' =>'4096k',
                       'mimeTypes' =>[
                            'image/jpeg',
                            'image/png',
                       ],
                       'mimeTypesMessage' => 'Dostępne formaty PNG/JPG',
                    ])

                ]
            ])
            ->add('guardian',EntityType::class,[
                'class' => User::class,
                'choice_label' => 'username',
                'mapped' => true,
                'multiple' => true,
                'required' => true,
                'query_builder' => function(UserRepository $users)
                {
                    $condition = true;
                    $admin = "ROLE_ADMIN";
                    $employee = "ROLE_PRACOWNIK";
                   return $users->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :admin')
                    ->setParameter('admin',"%$admin%") 
                    ->orWhere('u.roles LIKE :employee')
                    ->setParameter('employee',"%$employee%")
                    ->andWhere('u.available = :condition')
                    ->setParameter('condition', $condition)
                    ->orderBy('u.username','ASC'); 
                }
            ])
            ->add('status',EntityType::class,[
                'class' => Status::class,
                'choice_label' => 'StatusName',
                'mapped' => true,
                'multiple' => false,
                'required' => true,
                'query_builder' => function(StatusRepository $statuses) 
                {
                    return $statuses->createQueryBuilder('s')
                        ->where('s.refersTo LIKE :status')
                        ->setParameter('status', "%dog%")
                        ->orderBy('s.StatusName','ASC')              
                        ;
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
