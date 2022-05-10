<?php

namespace App\Form;

use App\Entity\Annuite;
use App\Data\AnnuiteFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AnnuiteFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('pays', EntityType::class, [
                'class' => Annuite::class,
                'required' => false,
                'label' => false,
                'choice_label' => 'pays',
            ])
            ->add('periode', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('montant', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('region', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            
            ;
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnnuiteFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}