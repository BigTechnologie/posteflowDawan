<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use App\Security\Voter\AgenceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/agence')]
class AgenceController extends AbstractController
{
    #[Route('', name: 'app_agence_index', methods: ['GET'])]
    public function index(
        Request $request,
        AgenceRepository $agenceRepository
    ): Response {
        /*
         * Tous les utilisateurs authentifiés peuvent
         * consulter la liste des agences.
         */
        $this->denyAccessUnlessGranted(
            AgenceVoter::LIST
        );

        $recherche = $request->query->getString('q');

        $agences = method_exists(
            $agenceRepository,
            'rechercher'
        )
            ? $agenceRepository->rechercher(
                $recherche !== '' ? $recherche : null
            )
            : $agenceRepository->findBy( 
                [],
                ['id' => 'DESC']
            );

        return $this->render('agence/index.html.twig', [
            'items' => $agences,
            'q' => $recherche,
        ]);
    }

    #[Route('/new', name: 'app_agence_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            AgenceVoter::CREATE
        );

        $agence = new Agence();

        $form = $this->createForm(
            AgenceType::class,
            $agence
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($agence);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L’agence a été créée avec succès.'
            );

            return $this->redirectToRoute(
                'app_agence_index'
            );
        }

        return $this->render('agence/form.html.twig', [
            'form' => $form,
            'item' => $agence,
        ]);
    }

    #[Route(
        '/{id}',
        name: 'app_agence_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(
        Agence $agence
    ): Response {
        
        $this->denyAccessUnlessGranted(
            AgenceVoter::VIEW,
            $agence
        );

        return $this->render('agence/show.html.twig', [
            'item' => $agence,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'app_agence_edit',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    public function edit(
        Agence $agence,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /*
         * La modification d’une agence est réservée
         * aux administrateurs.
         */
        $this->denyAccessUnlessGranted(
            AgenceVoter::EDIT,
            $agence
        );

        $form = $this->createForm(
            AgenceType::class,
            $agence
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L’agence a été modifiée avec succès.'
            );

            return $this->redirectToRoute(
                'app_agence_index'
            );
        }

        return $this->render('agence/form.html.twig', [
            'form' => $form,
            'item' => $agence,
        ]);
    }

    #[Route(
        '/{id}/delete',
        name: 'app_agence_delete',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function delete(
        Agence $agence,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            AgenceVoter::DELETE,
            $agence
        );

        $token = $request->request->getString('_token');

        if ($this->isCsrfTokenValid(
            'delete'.$agence->getId(),
            $token
        )) {
            $entityManager->remove($agence);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L’agence a été supprimée.'
            );
        } else {
            $this->addFlash(
                'danger',
                'Le jeton de sécurité est invalide. L’agence n’a pas été supprimée.'
            );
        }

        return $this->redirectToRoute(
            'app_agence_index'
        );
    }
}