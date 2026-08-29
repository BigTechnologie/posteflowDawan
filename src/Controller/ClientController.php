<?php
namespace App\Controller;
use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/client')]
class ClientController extends AbstractController
{
    // Cette méthode permet d'afficher la liste des clients
    #[Route('', name: 'app_client_index')] 
    public function index(Request $request, ClientRepository $repo): Response
    {
        $items = method_exists($repo, 'rechercher') ? $repo->rechercher($request->query->get('q')) : $repo->findBy([], ['id' => 'DESC']);
        return $this->render('client/index.html.twig', ['items' => $items, 'q' => $request->query->get('q')]);
    }

    // Cette méthode permet de créer un client
    #[Route('/new', name: 'app_client_new')] 
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $item = new Client();
        $form = $this->createForm(ClientType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();
            $this->addFlash('success', 'Enregistrement créé.');
            return $this->redirectToRoute('app_client_index');
        }
        return $this->render('client/form.html.twig', [
            'form' => $form, 'item' => $item]);
    }

    // Cette méthode permet d'afficher un client
    #[Route('/{id}', name: 'app_client_show', requirements: ['id' => '\\d+'])] 
    public function show(Client $item): Response
    {
        return $this->render('client/show.html.twig', ['item' => $item]);
    }

    // Cette méthode permet de modifier un client
    #[Route('/{id}/edit', name: 'app_client_edit')] 
    public function edit(Client $item, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ClientType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Modification enregistrée.');
            return $this->redirectToRoute('app_client_index');
        }
        return $this->render('client/form.html.twig', ['form' => $form, 'item' => $item]);
    }
    
    // Cette méthode permet de supprimer un client
    #[Route('/{id}/delete', name: 'app_client_delete', methods: ['POST'])] 
    public function delete(Client $item, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', 'Suppression effectuée.');
        }
        return $this->redirectToRoute('app_client_index');
    }
}
