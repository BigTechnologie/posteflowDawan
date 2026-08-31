<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use App\Entity\Client;
use App\Entity\Colis;
use App\Entity\MouvementColis;
use App\Entity\User;
use App\Enum\StatutColis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $objectManager): void
    {
        $utilisateurs = [
            [
                'email' => 'admin@posteflow.test',
                'nom' => 'Administrateur PosteFlow',
                'roles' => [
                    User::ROLE_ADMIN,
                ],
                'password' => 'admin1234',
            ],
            [
                'email' => 'agent@posteflow.test',
                'nom' => 'Agent PosteFlow',
                'roles' => [
                    User::ROLE_AGENT,
                ],
                'password' => 'agent1234',
            ],
            [
                'email' => 'user@posteflow.test',
                'nom' => 'Utilisateur PosteFlow',
                'roles' => [
                    User::ROLE_USER,
                ],
                'password' => 'user1234',
            ],
        ];

        foreach ($utilisateurs as $donneesUtilisateur) {
            $utilisateur = (new User())
                ->setEmail($donneesUtilisateur['email'])
                ->setNom($donneesUtilisateur['nom'])
                ->setRoles($donneesUtilisateur['roles']);

            $utilisateur->setPassword(
                $this->passwordHasher->hashPassword(
                    $utilisateur,
                    $donneesUtilisateur['password']
                )
            );

            $objectManager->persist($utilisateur);
        }

        /*
         * Création des agences.
         */
        $donneesAgences = [
            [
                'nom' => 'Paris Louvre',
                'codePostal' => '75001',
                'ville' => 'Paris',
                'adresse' => '12 rue du Louvre',
            ],
            [
                'nom' => 'Lyon Bellecour',
                'codePostal' => '69002',
                'ville' => 'Lyon',
                'adresse' => '4 place Bellecour',
            ],
            [
                'nom' => 'Marseille Colbert',
                'codePostal' => '13001',
                'ville' => 'Marseille',
                'adresse' => '8 rue Colbert',
            ],
        ];

        /** @var list<Agence> $agences */
        $agences = [];

        foreach ($donneesAgences as $donneesAgence) {
            $agence = (new Agence())
                ->setNom($donneesAgence['nom'])
                ->setCodePostal($donneesAgence['codePostal'])
                ->setVille($donneesAgence['ville'])
                ->setAdresse($donneesAgence['adresse'])
                ->setActive(true);

            $objectManager->persist($agence);

            $agences[] = $agence;
        }

        /*
         * Création des clients.
         */
        $nomsClients = [
            'Diallo',
            'Martin',
            'Bernard',
            'Petit',
            'Moreau',
            'Durand',
        ];

        /** @var list<Client> $clients */
        $clients = [];

        foreach ($nomsClients as $index => $nom) {
            $agence = $agences[
                $index % count($agences)
            ];

            $client = (new Client())
                ->setNom($nom)
                ->setEmail(
                    mb_strtolower($nom).'@example.test'
                )
                ->setTelephone(
                    sprintf('06000000%02d', $index)
                )
                ->setAdresse(
                    sprintf(
                        '%d rue de la Formation',
                        $index + 1
                    )
                )
                ->setCodePostal(
                    $agence->getCodePostal()
                )
                ->setVille(
                    $agence->getVille()
                )
                ->setAgenceReference($agence);

            $objectManager->persist($client);

            $clients[] = $client;
        }

        /*
         * Statuts disponibles sous forme d’objets enum.
         */
        $statuts = StatutColis::cases();

        /*
         * Création des colis et de leurs mouvements.
         */
        for ($index = 1; $index <= 18; ++$index) {
            $statut = $statuts[
                $index % count($statuts)
            ];

            $client = $clients[
                $index % count($clients)
            ];

            $agenceDepot = $agences[
                $index % count($agences)
            ];

            $agenceDestination = $agences[
                ($index + 1) % count($agences)
            ];

            $colis = (new Colis())
                ->setNumeroSuivi(
                    sprintf(
                        'LP%d%06d',
                        (int) date('Y'),
                        $index
                    )
                )
                ->setClient($client)
                ->setAgenceDepot($agenceDepot)
                ->setDestinataire(
                    sprintf('Destinataire %d', $index)
                )
                ->setAdresseLivraison(
                    sprintf(
                        '%d avenue du Courrier',
                        $index
                    )
                )
                ->setCodePostalLivraison(
                    $agenceDestination->getCodePostal()
                )
                ->setVilleLivraison(
                    $agenceDestination->getVille()
                )
                ->setPoidsKg(
                    random_int(1, 8) + 0.5
                )
                ->setStatut($statut)
                ->setCreatedAt(
                    new \DateTimeImmutable(
                        sprintf('-%d days', $index)
                    )
                );

            /*
             * Premier mouvement obligatoire :
             * création du colis.
             */
            $mouvementCreation = (new MouvementColis())
                ->setStatut(StatutColis::CREE)
                ->setLieu(
                    $agenceDepot->getVille()
                )
                ->setCommentaire(
                    'Création de l’envoi.'
                )
                ->setCreatedAt(
                    $colis->getCreatedAt()
                );

            $colis->addMouvement(
                $mouvementCreation
            );

            if ($statut !== StatutColis::CREE) {
                $mouvementStatut = (new MouvementColis())
                    ->setStatut($statut)
                    ->setLieu(
                        $colis->getVilleLivraison()
                    )
                    ->setCommentaire(
                        'Mise à jour automatique du statut.'
                    )
                    ->setCreatedAt(
                        new \DateTimeImmutable(
                            sprintf(
                                '-%d days +4 hours',
                                $index
                            )
                        )
                    );

                $colis->addMouvement(
                    $mouvementStatut
                );
            }

            $objectManager->persist($colis);
        }

        $objectManager->flush();
    }
}