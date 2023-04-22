<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Status;
use App\Repository\StatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content',TextareaType::class,[
                'label' => 'Dodaj Zdjęcie',
                'trim' => false,
                'mapped' => true,
                'required' => true,
            ])
            ->add('image',FileType::class,[
                'label' => 'Dodaj Zdjęcie',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Dostępne formaty PNG/JPG',
                    ])
                    
                ],

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
                        ->andwhere('s.refersTo LIKE :status')
                        ->setParameter('status', "%post%")
                        ->andWhere('s.archived = :archived')
                        ->setParameter('archived',false)
                        ->orderBy('s.StatusName','ASC')              
                        ;
                }
            ])

        ;
    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
