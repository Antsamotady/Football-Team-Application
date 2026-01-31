<?php

namespace App\Form;

use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('InstitutionName', TextType::class, [
                'required'  => false,
            ])
            ->add('passingScore', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'html5' => true,
            ])
            ->add('deliberationDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('AcademicYear', TextType::class, [
                'required'  => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
        ]);
    }
}
