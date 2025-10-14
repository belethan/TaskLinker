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

    // --- Route création ---
    #[Route('/projet/nouveau', name: 'projet.new')]
    public function add(Request $request, EmployeRepository $employeRepo): Response
    {
        return $this->addOrEdit(null, $request, $employeRepo);
    }

    // --- Route édition ---
    #[Route('/projet/{id}/edit', name: 'projet.edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request, EmployeRepository $employeRepo): Response
    {
        return $this->addOrEdit($id, $request, $employeRepo);
    }

    // --- Méthode commune création / édition ---
    private function addOrEdit(?int $id, Request $request, EmployeRepository $employeRepo): Response
    {
        $isEdit = (bool) $id;
        $projet = $isEdit ? $this->projetRepository->find($id) : new Projet();

        if ($isEdit && !$projet) {
            throw $this->createNotFoundException('Projet introuvable.');
        }

        // employés déjà liés pour pré-sélection
        $employesSelectionnes = $isEdit ? $projet->getEmployes()->toArray() : [];

        $form = $this->createForm(ProjectType::class, $projet, [
            'projet_id' => $isEdit ? $projet->getId() : null,
            'ajax_url' => $this->generateUrl('api_employes_disponibles', ['projetId' => $isEdit ? $projet->getId() : null]),
            'employes_selectiones' => $employesSelectionnes,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($projet);
            $this->entityManager->flush();

            $this->addFlash('success', $isEdit ? 'Projet modifié avec succès.' : 'Projet créé avec succès.');

            // après création, rediriger vers l'édition
            return $this->redirectToRoute('projet.index',);
        }

        return $this->render('projet/addMody.html.twig', [
            'formProjet' => $form->createView(),
            'projet' => $projet,
            'action' => $isEdit ? 'Modifier' : 'Nouveau Projet',
        ]);
    }

}
