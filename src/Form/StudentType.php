<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Student;
use App\Form\ScoreType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
            ->add('studentNumber', IntegerType::class, [
                'label' => "Numéro",
                'required' => false,
                'attr' => ['placeholder' => 'Matricule']
            ])
            ->add('gender', ChoiceType::class, [
                'required'    => true,
                'label'       => 'Sexe',
                'placeholder' => 'Veuiller sélectionner',
                'choices'     => [
                    'Masculin' => 'Mr',
                    'Féminin' => 'Me',
                ],
            ])
        ;

        if (!$options['classe_locked']) {
            $builder->add('classe', EntityType::class, [
                'class'         => Classe::class,
                'required'      => true,
                'label'         => 'Classe',
                'choice_label'  => 'name',
                'placeholder'   => 'Choisir la classe',
            ]);
        } else {
            $builder->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'name',
                'disabled' => true
            ]);
        }

        // Add scores collection
        if ($options['include_scores']) {
            $builder->add('scores', CollectionType::class, [
                'entry_type' => ScoreType::class,
                'entry_options' => ['label' => false],
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'label' => 'Notes',
                'attr' => ['class' => 'scores-collection'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => Student::class,
            'classe_locked' => false,
            'include_scores' => false
        ]);
    }
}
