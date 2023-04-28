<?php

namespace App\Form;

use DateTime;

use App\Entity\Livreur;
use App\Entity\Livraison;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class Livraison1backType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateLivraison', DateTimeType::class, [
            'data' => new \DateTime(),
        ])
            ->add('adresseLivraison')
            ->add('codePostalLivraison')
            ->add('statusLivraison', ChoiceType::class, [
                'choices' => [
                    'Confirmé' => 'confirmé',
                    'Annulé' => 'annulé',
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('idLivreur', EntityType::class, [
                'class' => Livreur::class,
                'choice_label' => 'nom',
            ])
            ->add('id_commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'id_commande',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livraison::class,
        ]);
    }
}
