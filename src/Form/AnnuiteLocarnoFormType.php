<?php

namespace App\Form;

use App\Entity\Annuite;
use App\Entity\AnnuiteLocarno;
use App\Repository\AnnuiteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AnnuiteLocarnoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('annuite', EntityType::class, [
                'class' => Annuite::class,
                'required' => false,
                'label' => false,
                'choice_label' => 'name',
                // 'query_builder' => function (AnnuiteRepository $annuiteRepository) {
                //     return $annuiteRepository->createQueryBuilder('u')->where('u.id = :id')->setParameter('id', $this->id);
                // },
            ])
            // ->add('region', TextType::class, [
            //     'disable' => $options['annuite'],
            // ])
            ->add('taxRegister')
            ->add('taxRenew')
            ->add('costViewRenew')
            ->add('costViewRegister')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnnuiteLocarno::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
