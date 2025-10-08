<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjetController extends AbstractController
{
    public function __construct(
        private ProjetRepository $projetRepository,
        private EntityManagerInterface $entityManager,
    ){}

    #[Route('/', name: 'projet')]
    public function index(): Response
    {
        $projets = $this->projetRepository->findAll();
        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
        ]);
    }
}
