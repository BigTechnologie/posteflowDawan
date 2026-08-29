<?php

namespace App\Form;

use App\Entity\Agence;
use App\Entity\Client;
use App\Entity\Colis;
use App\Enum\StatutColis;
use App\Repository\AgenceRepository;
use App\Repository\ClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class ColisType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('numeroSuivi', TextType::class, [
                'label' => 'Numéro de suivi',
                'help' => 'Le numéro doit être unique et contenir uniquement des lettres majuscules, des chiffres et des tirets.',
                'attr' => [
                    'placeholder' => 'PF-2026-000001',
                    'maxlength' => 50,
                    'autocomplete' => 'off',
                ],
            ])

            ->add('statut', EnumType::class, [
                'class' => StatutColis::class,

                'label' => 'Statut du colis',

                'choice_label' => fn(StatutColis $statut) => $statut->label(),

                'placeholder' => 'Choisir un statut',

                'help' => 'Le statut initial d’un nouveau colis est généralement « Créé ».',
            ])

            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Client expéditeur',
                'placeholder' => 'Choisir un client',
                'help' => 'Sélectionnez le client associé au colis.',
                'query_builder' => static function (
                    ClientRepository $clientRepository
                ) {
                    return $clientRepository
                        ->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => static function (
                    Client $client
                ): string {
                    return sprintf(
                        '%s — %s — %s',
                        $client->getNom(),
                        $client->getEmail(),
                        $client->getVille()
                    );
                },
            ])

            ->add('agenceDepot', EntityType::class, [
                'class' => Agence::class,
                'label' => 'Agence de dépôt',
                'placeholder' => 'Choisir une agence',
                'help' => 'Seules les agences actives sont proposées.',

                'query_builder' => static function (
                    AgenceRepository $agenceRepository
                ) {
                    return $agenceRepository
                        ->createQueryBuilder('a')
                        ->andWhere('a.active = :active')
                        ->setParameter('active', true)
                        ->orderBy('a.ville', 'ASC')
                        ->addOrderBy('a.nom', 'ASC');
                },

                'choice_label' => static function (
                    Agence $agence
                ): string {
                    return sprintf(
                        '%s — %s (%s)',
                        $agence->getNom(),
                        $agence->getVille(),
                        $agence->getCodePostal()
                    );
                },
            ])

            ->add('destinataire', TextType::class, [
                'label' => 'Nom du destinataire',
                'attr' => [
                    'placeholder' => 'Jean Dupont',
                    'maxlength' => 120,
                    'autocomplete' => 'name',
                ],
            ])

            ->add('adresseLivraison', TextType::class, [
                'label' => 'Adresse de livraison',
                'attr' => [
                    'placeholder' => '25 avenue de la République',
                    'maxlength' => 180,
                    'autocomplete' => 'street-address',
                ],
            ])

            ->add('codePostalLivraison', TextType::class, [
                'label' => 'Code postal de livraison',
                'attr' => [
                    'placeholder' => '75011',
                    'maxlength' => 5,
                    'inputmode' => 'numeric',
                    'autocomplete' => 'postal-code',
                ],
            ])

            ->add('villeLivraison', TextType::class, [
                'label' => 'Ville de livraison',
                'attr' => [
                    'placeholder' => 'Paris',
                    'maxlength' => 80,
                    'autocomplete' => 'address-level2',
                ],
            ])

            ->add('poidsKg', NumberType::class, [
                'label' => 'Poids du colis',
                'help' => 'Le poids doit être compris entre 0,01 et 30 kg.',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'placeholder' => '2.50',
                    'min' => 0.01,
                    'max' => 30,
                    'step' => 0.01,
                    'inputmode' => 'decimal',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Colis::class,
        ]);
    }
}

