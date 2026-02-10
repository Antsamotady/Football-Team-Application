<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
        ;
            
        if (!$options['location_locked']) {
            $builder->add('location', EntityType::class, [
                'class'         => Location::class,
                'required'      => true,
                'label'         => 'Centre',
                'choice_label'  => 'name',
                'placeholder'   => 'Choisir le centre',
            ]);
        } else {
            $builder->add('location', EntityType::class, [
                'label'         => 'Centre',
                'class' => Location::class,
                'choice_label' => 'name',
                'disabled' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
            'location_locked' => false
        ]);
    }
}
