<?php

namespace App\Form;

use App\Entity\Subject;
use App\Data\ScoreFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ScoreFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', NumberType::class, [
                'required' => false,
                'label' => 'Score min',
                'scale' => 2,
                'attr' => ['placeholder' => 'Min']
            ])
            ->add('max', NumberType::class, [
                'required' => false,
                'label' => 'Score max',
                'scale' => 2,
                'attr' => ['placeholder' => 'Max']
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'Toutes les matières',
                'label' => 'Matière',
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScoreFilterData::class,
        ]);
    }
}
