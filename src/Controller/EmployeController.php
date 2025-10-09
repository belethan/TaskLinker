<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
