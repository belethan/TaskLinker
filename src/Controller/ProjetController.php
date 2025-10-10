<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjectType;
use App\Repository\EmployeRepository;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjetController extends AbstractController
{
    public function __construct(
        private ProjetRepository $projetRepository,
        private EntityManagerInterface $entityManager,
    ){}

    #[Route('/', name: 'projet.index')]
    public function index(): Response
    {
        $projets = $this->projetRepository->findAll();
        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
        ]);
    }

    #[Route('/projet/nouveau', name: 'projet.new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $projet = new Projet();

        $form = $this->createForm(ProjectType::class, $projet, [
            'ajax_url' => $this->generateUrl('ajax_employe_list'),
            'employes_selectiones' =>  [], // aucun employé pré-sélectionné
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($projet);
            $em->flush();
            $this->addFlash('success', 'Projet créé avec succès.');
            return $this->redirectToRoute('projet.edit', ['id' => $projet->getId()]);
        }

        return $this->render('projet/addMody.html.twig', [
            'formProjet' => $form,
            'action' => 'Nouveau Projet',
            'projet' => $projet,
        ]);
    }

    #[Route('/Projet/{id}/edit', name: 'projet.edit',requirements: ['id' => '\d+'])]
    public function edit(Projet $projet, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjectType::class, $projet, [
            'projet_id' => $projet->getId(),
            'ajax_url' => $this->generateUrl('ajax_employe_list', ['projetId' => $projet->getId()]),
            'employes_selectiones' => $projet->getEmployes()->toArray(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            //$this->addFlash('success', 'Projet modifié avec succès.');

            return $this->redirectToRoute('projet.index');
        }

        return $this->render('projet/addMody.html.twig', [
            'formProjet' => $form,
            'projet' => $projet,
            'action' => 'Modifier',

        ]);
    }

    // --- AJAX pour Select2 ---
    #[Route('/ajax/employes/{projetId?}', name: 'ajax_employe_list')]
    public function ajaxEmployeList(?int $projetId, EmployeRepository $repo, Request $request): JsonResponse
    {
        $term = $request->query->get('q');
        $employes = $repo->AjaxfindEmployesDisponiblesOuAffectes($projetId);

        if ($term) {
            $employes = array_filter($employes, static fn($e) => stripos($e['nom'], $term) !== false);
        }

        $results = array_map(static fn($e) => ['id' => $e['id'], 'text' => $e['nom']], $employes);

        return new JsonResponse(['results' => array_values($results)]);
    }
}
