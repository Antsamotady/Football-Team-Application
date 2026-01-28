<?php

namespace App\Form;

use App\Entity\Score;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', NumberType::class, [
                'label' => $options['subject_name'],
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'placeholder' => 'Note',
                    'min' => 0,
                    'max' => 20,
                    'step' => 0.01,
                ],
                'html5' => true,
            ])
            ->add('feedback', TextareaType::class, [
                'label'     => 'Test from formtype',
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'Appréciations'
                ]
            ])
            ->add('subject', HiddenType::class, [
                'data' => $options['subject_id'],
                'mapped' => false, // We'll handle this in controller
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Score::class,
            'subject_name' => 'Note',
            'subject_id' => null,
        ]);
        
        $resolver->setAllowedTypes('subject_name', 'string');
        $resolver->setAllowedTypes('subject_id', ['int', 'null']);
    }
}
