<?php
// src/Repository/EmployeRepository.php
namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    public function findEmployesDisponiblesOuAffectes(?int $projetId, ?string $term = null, array $excludeIds = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.projets', 'p')
            ->addSelect('p');

        if ($projetId) {
            // En édition : inclure les employés libres ou déjà liés à ce projet
            $qb->andWhere('p.id IS NULL OR p.id = :projetId')
                ->setParameter('projetId', $projetId);
        } else {
            // En création : uniquement les employés non liés
            $qb->andWhere('p.id IS NULL');
        }

        // Exclure des employés déjà sélectionnés côté client
        if (!empty($excludeIds)) {
            $qb->andWhere('e.id NOT IN (:excludeIds)')
                ->setParameter('excludeIds', $excludeIds);
        }

        // Filtrer par recherche textuelle
        if (!empty($term)) {
            $qb->andWhere('e.nom LIKE :term OR e.prenom LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('e.nom', 'ASC');
    }

}
