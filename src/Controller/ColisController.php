<?php

namespace App\Controller;

use App\Entity\Colis;
use App\Entity\MouvementColis;
use App\Enum\StatutColis;
use App\Form\ColisType;
use App\Form\MouvementColisType;
use App\Repository\ColisRepository;
use App\Security\Voter\ColisVoter;
use App\Service\TrackingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/colis')]
class ColisController extends AbstractController
{
    #[Route('', name: 'app_colis_index', methods: ['GET'])]
    public function index(
        Request $request,
        ColisRepository $colisRepository
    ): Response 
    {
        
        $this->denyAccessUnlessGranted(
            ColisVoter::LIST
        );

        $recherche = $request->query->getString('q');
        $statutValeur = $request->query->getString('statut');
        $ville = $request->query->getString('ville');

        $statut = $statutValeur !== ''
            ? StatutColis::tryFrom($statutValeur)
            : null;

        return $this->render('colis/index.html.twig', [
            'items' => $colisRepository->rechercher(
                $recherche !== '' ? $recherche : null,
                $statut,
                $ville !== '' ? $ville : null
            ),
            'statuts' => StatutColis::cases(),
            'filtres' => [
                'q' => $recherche,
                'statut' => $statutValeur,
                'ville' => $ville,
            ],
        ]);
    }

    #[Route('/new', name: 'app_colis_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ColisVoter::CREATE
        );

        $colis = new Colis();

        $form = $this->createForm(
            ColisType::class,
            $colis
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($colis);
            $entityManager->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Le colis %s a été créé avec succès.',
                    $colis->getNumeroSuivi()
                )
            );

            return $this->redirectToRoute(
                'app_colis_index'
            );
        }

        return $this->render('colis/form.html.twig', [
            'form' => $form,
            'colis' => $colis,
            'page_title' => 'Créer un colis',
        ]);
    }

    #[Route('/{id}', name: 'app_colis_show', methods: ['GET', 'POST'])]
    public function show(
        Colis $colis,
        Request $request,
        TrackingService $trackingService
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ColisVoter::VIEW,
            $colis 
        );

        $mouvement = (new MouvementColis())
            ->setColis($colis);

        $form = $this->createForm(
            MouvementColisType::class,
            $mouvement
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            $this->denyAccessUnlessGranted(
                ColisVoter::CHANGE_STATUS,
                $colis
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $trackingService->changerStatut(
                $colis,
                $mouvement->getStatut(),
                $mouvement->getLieu(),
                $mouvement->getCommentaire()
            );

            $this->addFlash(
                'success',
                sprintf(
                    'Le colis %s possède maintenant le statut « %s ».',
                    $colis->getNumeroSuivi(),
                    $mouvement->getStatut()->label()
                )
            );

            return $this->redirectToRoute(
                'app_colis_show',
                [
                    'id' => $colis->getId(),
                ]
            );
        }

        return $this->render('colis/show.html.twig', [
            'colis' => $colis,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_colis_edit', methods: ['GET', 'POST'])]
    // #[IsGranted(attribute: ColisVoter::EDIT, subject: 'colis')]
    public function edit(
        Colis $colis,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ColisVoter::EDIT,
            $colis
        );

        $form = $this->createForm(
            ColisType::class,
            $colis
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Le colis %s a été modifié avec succès.',
                    $colis->getNumeroSuivi()
                )
            );

            return $this->redirectToRoute(
                'app_colis_show',
                [
                    'id' => $colis->getId(),
                ]
            );
        }

        return $this->render('colis/form.html.twig', [
            'form' => $form,
            'colis' => $colis,
            'page_title' => 'Modifier un colis',
        ]);
    }

    #[Route(
        '/{id}/delete',
        name: 'app_colis_delete',
        methods: ['POST']
    )]
    public function delete(
        Colis $colis,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ColisVoter::DELETE,
            $colis
        );

        $token = $request->request->getString('_token');

        if ($this->isCsrfTokenValid(
            'delete'.$colis->getId(),
            $token
        )) {
            $numeroSuivi = $colis->getNumeroSuivi();

            $entityManager->remove($colis);
            $entityManager->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Le colis %s a été supprimé.',
                    $numeroSuivi
                )
            );
        } else {
            $this->addFlash(
                'danger',
                'Le jeton de sécurité est invalide. Le colis n’a pas été supprimé.'
            );
        }

        return $this->redirectToRoute(
            'app_colis_index'
        );
    }
}