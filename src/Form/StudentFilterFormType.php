<?php

namespace App\Form;

use App\Entity\Classe;
use App\Data\StudentFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
                'placeholder'   => 'Choisir la civilité',
                'choices' => [
                    'Mr' => 'Mr',
                    'Me' => 'Me'
                ],
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder'   => 'Choisir la classe',
            ])
            ;
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}