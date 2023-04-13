<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Echanges;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Echanges1backType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('statut', ChoiceType::class, [
            'choices' => [
                'Confirmé' => 'confirmé',
                'Annulé' => 'annulé',
            ],
            'expanded' => false,
            'multiple' => false,
            'required' => true,
        ])
            ->add('commentaire')
            ->add('produitEchange', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nomProduit',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.idUser = :userId')
                        ->setParameter('userId', 13);
                },
            ])
            ->add('produitOffert', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nomProduit',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.idUser = :userId')
                        ->setParameter('userId', 12);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Echanges::class,
        ]);
    }
}
