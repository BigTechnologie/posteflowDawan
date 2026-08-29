<?php

namespace App\Controller;

use App\Enum\StatutColis;
use App\Repository\AgenceRepository;
use App\Repository\ClientRepository;
use App\Repository\ColisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        ColisRepository $colisRepository,
        ClientRepository $clientRepository,
        AgenceRepository $agenceRepository
    ): Response {
        $statistiques = $colisRepository
            ->rechercherPourTableauDeBord();

        $statistiques['parStatut'] = array_map(
            static function (array $ligne): array {
                $statut = $ligne['statut'];

                if (!$statut instanceof StatutColis) {
                    $statut = StatutColis::from($statut);
                }

                return [
                    'statut' => $statut,
                    'total' => (int) $ligne['total'],
                ];
            },
            $statistiques['parStatut']
        );

        return $this->render('dashboard/index.html.twig', [
            'stats' => $statistiques,
            'nbClients' => $clientRepository->count([]),
            'nbAgences' => $agenceRepository->count([]),
            'villes' => $clientRepository->compterParVille(),
        ]);
    }
}