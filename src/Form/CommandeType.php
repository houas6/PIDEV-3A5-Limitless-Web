<?php

namespace App\Form;

use App\Entity\Commande;
use App\Form\Type\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;




class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ])
        ->add('prenom', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ])
        ->add('adresse', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);

       
        
        
       
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
