<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Student;
use App\Form\SubjectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('gender')
            ->add('classe', EntityType::class, [
                'class'         => Classe::class,
                'required'      => false,
                'choice_label'  => 'name'
            ])
            ->add('subjects', CollectionType::class, [
                'entry_type'    => SubjectType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'entry_options'  => [
                    'label'     => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
