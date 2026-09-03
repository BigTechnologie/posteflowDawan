<?php

namespace App\Form;

use App\DTO\ColisSearchDTO;
use App\Enum\StatutColis;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColisSearchType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('terme', SearchType::class, [
                'label' => 'Recherche',
                'required' => false,

                'attr' => [
                    'placeholder' => 'N° suivi, destinataire ou client',
                    'maxlength' => 100,
                    'autocomplete' => 'off', // désactive l'autocomplete du navigateur
                ],
            ])

            ->add('ville', SearchType::class, [
                'label' => 'Ville de livraison',

                'required' => false,

                'attr' => [
                    'placeholder' => 'Toulouse',
                    'maxlength' => 80,
                    'autocomplete' => 'off',
                ],
            ])

            ->add('statut', EnumType::class, [
                'class' => StatutColis::class,

                'label' => 'Statut',

                'required' => false,

                'placeholder' => 'Tous les statuts',

                'choice_label' => static function (
                    StatutColis $statut
                ): string {
                    return $statut->label();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Le formulaire hydrate directement le DTO
            'data_class' => ColisSearchDTO::class,

            // Le formulaire de recherche utilise la méthode GET
            'method' => 'GET', // /colis?terme=justine&ville=Nantes&statut=CREE

            // Nous désactivons la protection csrf
            'csrf_protection' => false,
        ]);
    }

    // Evite d'avoir de prefixe dans l'URL
    public function getBlockPrefix(): string
    {
        return '';
    }

}
