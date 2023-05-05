<?php

namespace App\Form;

use DateTime;
use App\Entity\Livreur;
use App\Entity\Livraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class Livraison1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateLivraison', DateTimeType::class, [
            'data' => new DateTime(),
        ])
            ->add('adresseLivraison')
            ->add('codePostalLivraison')
            ->add('statusLivraison', null, [
                'data' => 'en cours',
                'disabled' => true,
            ])
            ->add('idLivreur', EntityType::class, [
                'class' => Livreur::class,
                'choice_label' => 'nom',
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