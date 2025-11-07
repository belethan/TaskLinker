<?php

namespace App\Repository;

use App\Entity\Projet;
use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    /**
     * Liste les projets non archivés.
     * @return array
     */
    public function findNonArchives(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.archiver = 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les projets non archivés pour un employé donné.
     *
     * - Si l'employé a le rôle ADMIN → tous les projets non archivés.
     * - Sinon → uniquement les projets auxquels il est affecté.
     *
     * @param Employe $employe L'employé connecté
     * @return Projet[] Liste des projets visibles pour cet employé
     */
    public function findNonArchivesByEmploye(Employe $employe): array
    {
        // Si c’est un administrateur → retourne tous les projets non archivés
        if (in_array('ROLE_ADMIN', $employe->getRoles(), true)) {
            return $this->findNonArchives();
        }

        // Sinon, retourne uniquement les projets où il est affecté
        return $this->createQueryBuilder('p')
            ->innerJoin('p.employes', 'e')     // relation ManyToMany
            ->andWhere('p.archiver = 0')       // projets actifs
            ->andWhere('e = :employe')         // restreint à l'utilisateur connecté
            ->setParameter('employe', $employe)
            ->getQuery()
            ->getResult();
    }



}
