<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Nom']
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Prénom']
            ])
            ->add('gender', ChoiceType::class, [
                'required'    => true,
                'label'       => false,
                'placeholder' => 'Choisir la civilité',
                'choices'     => [
                    'Mr' => 'Mr',
                    'Me' => 'Me'
                ],
                'expanded' => true,
            ])
            ->add('classe', EntityType::class, [
                'class'         => Classe::class,
                'required'      => true,
                'label'         => false,
                'choice_label'  => 'name',
                'placeholder'   => 'Choisir la classe',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => Student::class
        ]);
    }
}
