<?php

namespace App\Form;

use App\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('StatusName')
            ->add('refersTo')
            ->add('refersTo',ChoiceType::class,array(
                'choices' => array(
                    'Dotyczy' =>array(
                        'Adopcji' => 'case',
                        'Psa'=>'dog',
                        'Użytkownika'=>'user',
                        'Dokumentu' => 'document',
                        'Posta' => 'post',
                    )
                    ),
                    'multiple'=>true,
                    'required'=>true,
                    'disabled'=>false,
            ))
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
        ]);
    }
}
