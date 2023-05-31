<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Player;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('country')
            ->add('money')
            ->add('player', EntityType::class, [
                'class' => Player::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('p')
                        ->leftJoin('p.team', 't')
                        ->where('t.id IS NULL');
                },
                'choice_label' => 'fullname',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Add player(s)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
