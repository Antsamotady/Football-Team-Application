<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Location;
use App\Data\ClasseFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ClasseFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la classe',
                'required' => false,
                'attr' => ['placeholder' => 'Nom de la classe']
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'Choisir le centre',
                'label' => 'Salle',
            ])
            ;
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClasseFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}