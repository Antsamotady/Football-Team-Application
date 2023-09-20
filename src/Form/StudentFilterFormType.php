<?php

namespace App\Form;

use App\Data\StudentFilterData;
use App\Entity\Classe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StudentFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Nom']
            ])
            ->add('fanampiny', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Prénom']
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'required' => false,
                // 'attr' => ['placeholder' => 'Classe'],
                'choice_label' => 'name'
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