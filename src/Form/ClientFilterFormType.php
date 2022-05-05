<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Data\ClientFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ClientFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => Abonnement::class,
                'required' => false,
                'label' => false,
                'choice_label' => 'nomClient',
            ])
            ->add('cleAbo', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('flagActif', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [0,1],
            ])
            ->add('nbTitre', IntegerType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('nbUsers', IntegerType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('nbEntities', IntegerType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('simulation', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [0,1],
            ])
            ->add('limitAnnonce', IntegerType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('dateMin', DateType::class, [
                'required' => false,
                'label' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('dateMax', DateType::class, [
                'required' => false,
                'label' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ])
            ;
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClientFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}