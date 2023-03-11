<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Documents;
use App\Repository\StatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DocumentsType extends AbstractType
{
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
            ->add('adoptionCase')
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
