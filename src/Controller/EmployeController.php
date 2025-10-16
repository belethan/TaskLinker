<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class EmployeController extends AbstractController
{
    public function __construct(
        private EmployeRepository $EmployeRepository,
        //private EntityManagerInterface $entityManager,
    ){}
    #[Route('/employe', name: 'employe')]
    public function index(): Response
    {
        $employes = $this->EmployeRepository->findAll();
        return $this->render('employe/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[Route('/employe/{id}/editer', name:'employe_edit')]
    public function edit(Employe $employes,Request $request, EntityManagerInterface $em): Response
    {
        // création du formulaire
        $formEmploye = $this->createForm(EmployeType::class, $employes);
        // traitement du formulaire pour mettre à jour les champs dans le cas ou redirige vers le formulaire
        $formEmploye->handleRequest($request);
        // bloc de validation
        if ($formEmploye->isSubmitted() && $formEmploye->isValid()) {
            $em->persist($employes);
            $em->flush();
            return $this->redirectToRoute('employe');
        }
        return $this->render('employe/edit.html.twig', [
            'employe' => $employes,
            'formEmploye' => $formEmploye,
        ]);
    }

    #[Route('/employe/{id}/supprimer', name: 'employe.delete')]
    public function delete(Employe $employe, EntityManagerInterface $em, Request $request): Response
    {
        // Vérifie si l'employé est associé à au moins un projet
        if (!$employe->getProjets()->isEmpty()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer cet employé car il est associé à au moins un projet.');
            return $this->redirectToRoute('employe');
        }

        // Suppression de l'employé
        $em->remove($employe);
        $em->flush();

        $this->addFlash('success', 'L\'employé a bien été supprimé.');
        return $this->redirectToRoute('employe');
    }


    #[Route('/api/employes/disponibles', name: 'api_employes_disponibles', methods: ['GET'])]
    public function ajaxDispo(Request $request, EmployeRepository $employeRepo): JsonResponse
    {
        // Récupération des paramètres AJAX
        $projetId = (int) $request->query->get('projet', 0);
        $idsAExclure = $request->query->all('exclude') ?: []; // tableau d’IDs à exclure (Select2 enverra peut-être une string → tableau)

        // On construit la requête (QueryBuilder)
        $qb = $employeRepo->findEmployesDisponiblesOuAffectesNative($projetId, $idsAExclure);

        // On exécute la requête Doctrine
        $employes = $qb->getQuery()->getResult();

        // Formatage des données pour Select2
        $results = array_map(fn($e) => [
            'id'   => $e->getId(),
            'text' => $e->getNom() . ' ' . $e->getPrenom(),
        ], $employes);

        // Retour JSON conforme au format attendu par Select2
        return $this->json([
            'results' => $results,
        ]);
    }

}
