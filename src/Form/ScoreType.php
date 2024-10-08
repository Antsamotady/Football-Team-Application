<?php

namespace App\Form;

use App\Entity\Score;
use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', EntityType::class, [
                'class'         => Subject::class,
                'choice_label'  => 'name',
                'placeholder'   => 'Select a subject',
                'required'      => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'label' => false,
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('value', null, [
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Score::class,
        ]);
    }
}
