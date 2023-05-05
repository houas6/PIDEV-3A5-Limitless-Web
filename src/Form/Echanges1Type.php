<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Echanges;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Echanges1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', null, [
                'data' => 'en cours',
                'disabled' => true,
            ])
            ->add('commentaire')
            ->add('produitEchange', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nomProduit',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.idUser = :userId')
                        ->setParameter('userId', 12);
                },
            ])
            ->add('produitOffert', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nomProduit',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.idUser = :userId')
                        ->setParameter('userId', 1);
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
