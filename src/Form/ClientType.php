<?php

namespace App\Form;

use App\Entity\Agence;
use App\Entity\Client;
use App\Repository\AgenceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du client',
                'help' => 'Indiquez le nom complet du client.',
                'attr' => [
                    'placeholder' => 'Kandia Diallo',
                    'maxlength' => 100,
                    'autocomplete' => 'name',
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'help' => 'Cette adresse pourra être utilisée pour les notifications de suivi.',
                'attr' => [
                    'placeholder' => 'client@example.com',
                    'maxlength' => 120,
                    'autocomplete' => 'email',
                ],
            ])

            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'help' => 'Exemple : 06 12 34 56 78.',
                'attr' => [
                    'placeholder' => '06 12 34 56 78',
                    'maxlength' => 30,
                    'autocomplete' => 'tel',
                    'inputmode' => 'tel',
                ],
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => '10 rue des Lilas',
                    'maxlength' => 180,
                    'autocomplete' => 'street-address',
                ],
            ])

            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => '31000',
                    'maxlength' => 5,
                    'inputmode' => 'numeric', 
                    'autocomplete' => 'postal-code', 
                ],
            ])

            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Toulouse',
                    'maxlength' => 80,
                    'autocomplete' => 'address-level2', 
                ],
            ])

            ->add('agenceReference', EntityType::class, [
                'class' => Agence::class,
                'label' => 'Agence de référence',
                'placeholder' => 'Choisir une agence',
                'help' => 'Sélectionnez l’agence à laquelle le client est rattaché.',

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

                
                'choice_label' => static function (Agence $agence): string { 
                    return sprintf(
                        '%s — %s (%s)', 
                        $agence->getNom(), 
                        $agence->getVille(), 
                        $agence->getCodePostal() 
                    );
                },
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}