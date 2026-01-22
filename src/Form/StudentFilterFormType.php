<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Location;
use App\Data\StudentFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class StudentFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                'required' => false,
                'label' => 'Civilité',
                'placeholder'   => 'Choisir le sexe',
                'choices' => [
                    'Masculin' => 'Mr',
                    'Féminin' => 'Me'
                ],
            ])
        ;

        if (!$options['classe_locked']) {
            $builder->add('classe', EntityType::class, [
                'class' => Classe::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder'   => 'Choisir la classe',
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'Choisir le centre',
                'label' => 'Salle',
            ]);
        }

        $builder
            ->add('scoreFilters', CollectionType::class, [
                'entry_type' => ScoreFilterFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'label' => 'test---',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentFilterData::class,
            'method' => 'GET',
            'classe_locked' => false,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}