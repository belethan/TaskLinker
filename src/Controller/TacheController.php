<?php

namespace App\Controller;

use App\Entity\Taches;
use App\Entity\Projet;
use App\Form\TacheType;
use App\Repository\ProjetRepository;
use App\Repository\TachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class TacheController extends AbstractController
{


    public function __construct(
       private EntityManagerInterface $em,
       private TachesRepository $tacheRepository,
       private ProjetRepository $projetRepository
       ){}


    #[Route('/tache/{id}', name:'tache.index', requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {
        // Récupération du projet par son ID si archiver = 0
        $projet = $this->em->getRepository(Projet::class)->findOneBy([
            'id' => $id,
            'archiver' => 0
        ]);
        // protection si jamais ID n'existe pas dans la table.
        if (!$projet) {
            throw $this->createNotFoundException('Le projet n\'existe pas');
        }
        // Récupération des employés associés au projet (ManyToMany)
        $employes = $projet->getEmployes(); // Doctrine renvoie une Collection
        // ✅ Vérification : si aucun employé n'est associé au projet
        if ($employes->isEmpty()) {
            $this->addFlash('warning', 'On ne peut pas ajouter ou modifier une tâche si aucun employé n\'est affecté au projet.');
            return $this->redirectToRoute('projet.index');
        }
        // Récupération des tâches liées à ce projet
        $taches = $this->tacheRepository->findBy(['projet' => $projet]);

        return $this->render('tache/index.html.twig', [
            'projet' => $projet,
            'employes' => $employes,
            'taches' => $taches,
        ]);
    }

    // ============================================================
    // =============== AJOUTER UNE TÂCHE ==========================
    // ============================================================
    #[Route('/tache/ajouter/{projetId}', name: 'tache.ajouter')]
    public function ajouter(int $projetId, Request $request): Response
    {
        $projet = $this->projetRepository->find($projetId);

        if (!$projet) {
            $this->addFlash('danger', 'Projet introuvable.');
            return $this->redirectToRoute('projet.index');
        }

        if ($projet->getEmployes()->count() === 0) {
            $this->addFlash('warning', 'Aucun employé associé à ce projet. Impossible de créer une tâche.');
            return $this->redirectToRoute('projet.index');
        }

        $tache = new Taches();
        $tache->setProjet($projet);

        return $this->handleTacheForm($tache, $request, $projet, false);
    }

    // ============================================================
    // =============== MODIFIER UNE TÂCHE =========================
    // ============================================================
    #[Route('/tache/modifier/{id}', name: 'tache.modifier')]
    public function modifier(int $id, Request $request): Response
    {
        $tache = $this->tacheRepository->find($id);

        if (!$tache) {
            $this->addFlash('danger', 'Tâche introuvable.');
            return $this->redirectToRoute('projet.index');
        }

        $projet = $tache->getProjet();

        return $this->handleTacheForm($tache, $request, $projet, true);
    }

    // ============================================================
    // =============== SUPPRIMER UNE TÂCHE =========================
    // ============================================================
    #[Route('/tache/{id}/supprimer', name: 'tache.delete')]
    public function delete(Taches $tache, EntityManagerInterface $em): Response
    {
        $projet = $tache->getProjet();

        $em->remove($tache);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        // Redirection vers la liste des tâches du projet
        return $this->redirectToRoute('tache.index', ['id' => $projet->getId()]);
    }


    // ============================================================
    // =============== MÉTHODES PRIVÉES ===========================
    // ============================================================

    /**
     * Gère le formulaire commun pour création et modification.
     */
    private function handleTacheForm(
        Taches $tache,
        Request $request,
        Projet $projet,
        bool $isEdition
    ): Response {
        $form = $this->createForm(TacheType::class, $tache, [
            'projet_id' => $projet->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($tache);
            $this->em->flush();

            $message = $isEdition
                ? 'Tâche mise à jour avec succès.'
                : 'Tâche créée avec succès.';

            $this->addFlash('success', $message);

            return $this->redirectToRoute('tache.index', [
                'id' => $projet->getId(),
            ]);
        }

        return $this->render('tache/addEditTache.html.twig', [
            'form' => $form->createView(),
            'projet' => $projet,
            'tache' => $tache,
            'isEdition' => $isEdition,
            'titrePage' => $isEdition
                ? "Modifier la tâche : " . $tache->getTitre()
                : "Créer une tâche pour le projet : " . $projet->getNom(),
        ]);
    }
}
