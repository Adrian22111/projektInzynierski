<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Documents;
use App\Entity\AdoptionCase;
use App\Repository\AdoptionCaseRepository;
use App\Repository\StatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Security;

class DocumentsType extends AbstractType
{
    public function __construct(private Security $security)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('documentName')
            ->add('documentSource',FileType::class,[
                'label' => 'Dokumenty (.pdf/.docx)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Dostępne formaty to PDF/DOCX'
                    ])
                    ],
                
            ])
            ->add('adoptionCase',EntityType::class,[
                'class' => AdoptionCase::class,
                'choice_label' => 'dog',
                'mapped' => true,
                'multiple' => false,
                'required' => false,
                'query_builder' => function(AdoptionCaseRepository $cases) 
                {
                    
                    if($this->security->isGranted('ROLE_ADMIN'))
                    {
                        return $cases->createQueryBuilder('c')
                        ->where('c.archived = :archived')
                        ->setParameter('archived', false)
                        ->orderBy('c.id','ASC')              
                        ;
                    }
                    else
                    {
                        //tutaj pracownik itp zwrocic tylko jego sprawy
                        return $cases->createQueryBuilder('c')
                        ->where('c.archived = :archived')
                        ->setParameter('archived', false)
                        ->innerJoin('c.employee','e')
                        ->andWhere('e = :id')
                        ->setParameter('id',$this->security->getUser())
                        ->orderBy('c.id','ASC')              
                        ;
                    }

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
                        ->setParameter('status', "%document%")
                        ->orderBy('s.StatusName','ASC')              
                        ;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Documents::class,
        ]);
    }
}
