<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Security\Voter\ClientVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('', name: 'app_client_index', methods: ['GET'])]
    public function index(
        Request $request,
        ClientRepository $clientRepository
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ClientVoter::LIST
        );

        $recherche = $request->query->getString('q');

        $clients = method_exists(
            $clientRepository,
            'rechercher'
        )
            ? $clientRepository->rechercher(
                $recherche !== '' ? $recherche : null
            )
            : $clientRepository->findBy(
                [],
                ['id' => 'DESC']
            );

        return $this->render('client/index.html.twig', [
            'items' => $clients,
            'q' => $recherche,
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ClientVoter::CREATE
        );

        $client = new Client();

        $form = $this->createForm(
            ClientType::class,
            $client
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le client a été créé avec succès.'
            );

            return $this->redirectToRoute(
                'app_client_index'
            );
        }

        return $this->render(
            'client/form.html.twig',
            [
                'form' => $form,
                'item' => $client,
            ]
        );
    }

    #[Route('/{id}', name: 'app_client_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        Client $client
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ClientVoter::VIEW,
            $client
        );

        return $this->render(
            'client/show.html.twig',
            [
                'item' => $client,
            ]
        );
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(
        Client $client,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ClientVoter::EDIT,
            $client
        );

        $form = $this->createForm(
            ClientType::class,
            $client
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le client a été modifié avec succès.'
            );

            return $this->redirectToRoute(
                'app_client_index'
            );
        }

        return $this->render(
            'client/form.html.twig',
            [
                'form' => $form,
                'item' => $client,
            ]
        );
    }

    #[Route('/{id}/delete', name: 'app_client_delete', methods: ['POST'])]
    public function delete(
        Client $client,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        
        $this->denyAccessUnlessGranted(
            ClientVoter::DELETE,
            $client
        );

        if (
            $this->isCsrfTokenValid(
                'delete'.$client->getId(),
                $request->request->getString('_token')
            )
        ) {
            $entityManager->remove($client);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le client a été supprimé.'
            );
        }

        return $this->redirectToRoute(
            'app_client_index'
        );
    }
}