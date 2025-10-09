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
        private EntityManagerInterface $entityManager,
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

    #[Route('/api/employes/disponibles', name: 'api_employes_disponibles', methods: ['GET'])]
    public function getEmployesDisponibles(
        Request $request,
        EmployeRepository $employeRepository
    ): JsonResponse
    {
        $search = $request->query->get('q', '');
        $projetId = $request->query->get('projet_id', null);

        $qb = $employeRepository->createQueryBuilder('e')
            ->leftJoin('e.projets', 'p');

        // Filtrer les employés déjà affectés à d'autres projets
        if ($projetId) {
            // En mode édition : exclure les employés affectés à d'autres projets
            // mais inclure ceux déjà affectés à CE projet
            $qb->andWhere('p.id IS NULL OR p.id = :projetId')
                ->setParameter('projetId', $projetId);
        } else {
            // En mode création : exclure tous les employés déjà affectés
            $qb->andWhere('p.id IS NULL');
        }

        // Recherche par nom ou prénom
        if (!empty($search)) {
            $qb->andWhere('e.nom LIKE :search OR e.prenom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.prenom', 'ASC')
            ->setMaxResults(50); // Limiter les résultats

        $employes = $qb->getQuery()->getResult();

        // Formater pour Select2
        $results = [];
        foreach ($employes as $employe) {
            $results[] = [
                'id' => $employe->getId(),
                'text' => $employe->getNom() . ' ' . $employe->getPrenom(),
            ];
        }
        return $this->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);
    }

}
