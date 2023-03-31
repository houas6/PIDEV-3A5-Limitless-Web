<?php

namespace App\Form;
use App\Entity\Categorie;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_produit')
        ->add('description')
        ->add('prix')
        ->add('image',FileType::class, array('data_class' => null,'required' => false))
        ->add('idUser')
        ->add('idcategorie', EntityType::class, [
            'class' => Categorie::class,
            'choice_label' => 'nomcategorie',
    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            
        ]);
    }
}
