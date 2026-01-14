<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Subject;
use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Masculin' => 'M',
                    'Féminin' => 'Me',
                ],
                'placeholder' => 'Veuiller sélectionner',
            ])
            ->add('classe', EntityType::class, [
                'label' => 'Classes',
                'class' => Classe::class,
                'choice_label' => 'name', // Display class name
                'multiple' => true, // Allow multiple selection
                'expanded' => false, // false = select dropdown, true = checkboxes
                'required' => false,
                'attr' => [
                    'class' => 'form-select form-select-lg form-select-solid',
                    'data-control' => 'select2',
                    'data-placeholder' => 'Sélectionnez les classes',
                    'data-allow-clear' => 'true',
                ],
            ])
            ->add('subjects', EntityType::class, [
                'label' => 'Matière(s)',
                'class' => Subject::class,
                'choice_label' => 'name', // Display class name
                'multiple' => true, // Allow multiple selection
                'expanded' => false, // false = select dropdown, true = checkboxes
                'required' => false,
                'attr' => [
                    'class' => 'form-select form-select-lg form-select-solid',
                    'data-control' => 'select2',
                    'data-placeholder' => 'Sélectionnez les matières',
                    'data-allow-clear' => 'true',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class
        ]);
    }
}
