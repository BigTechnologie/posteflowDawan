<?php

namespace App\Form;

use App\Entity\MouvementColis;
use App\Enum\StatutColis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MouvementColisType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('statut', EnumType::class, [
                
                'class' => StatutColis::class,

                'label' => 'Nouveau statut',

               
                'choice_label' => static function (
                    StatutColis $statut
                ): string {
                    return $statut->label();
                },

                'placeholder' => 'Choisir un nouveau statut',

                'help' => 'Sélectionnez le nouveau statut du colis.',

                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('lieu', TextType::class, [
                'label' => 'Lieu du mouvement',

                'help' => 'Indiquez l’agence, le centre de tri ou la ville concernée.',

                'attr' => [
                    'placeholder' => 'Centre de tri de Toulouse',
                    'maxlength' => 160,
                    'autocomplete' => 'off',
                ],
            ])

            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',

                'required' => false,

                'help' => 'Ajoutez une précision facultative sur ce mouvement.',

                'attr' => [
                    'placeholder' => 'Colis arrivé au centre de tri.',
                    'maxlength' => 1000,
                    'rows' => 4,
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => MouvementColis::class,
        ]);
    }
}