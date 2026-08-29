<?php

namespace App\Form;

use App\Entity\Agence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgenceType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {

        $builder

            ->add('nom', TextType::class, [
                'label' => 'Nom de l’agence', 

                'help' => 'Nom officiel de l’agence.', 

                'attr' => [ 
                    'class' => 'form-control', 
                    'placeholder' => 'Agence Toulouse Centre', 
                    'maxlength' => 120, 
                    'autocomplete' => 'organization', 
                ],
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Adresse',

                'attr' => [ 
                    'class' => 'form-control', 
                    'placeholder' => '10 rue des Lilas', 
                    'maxlength' => 180, 
                    'autocomplete' => 'street-address', 
                ],
            ])

            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',

                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '31000',
                    'maxlength' => 5,
                    'inputmode' => 'numeric',
                    'autocomplete' => 'postal-code',
                ],
            ])

            ->add('ville', TextType::class, [
                'label' => 'Ville',

                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Toulouse',
                    'maxlength' => 80,
                    'autocomplete' => 'address-level2',
                ],
            ])

            ->add('active', CheckboxType::class, [
                'label' => 'Agence active',

                'required' => false,

                'help' => 'Décochez cette case pour désactiver temporairement l’agence.',
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {

        $resolver->setDefaults([
            'data_class' => Agence::class,
        ]);
    }
}