<?php

namespace App\Form;

use App\Data\ExtensionsFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExtensionsFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pays', TextType::class, [
                'required' => false,
                'label' => false,
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
            'data_class' => ExtensionsFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}