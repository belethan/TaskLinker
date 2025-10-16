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


    public function findEmployesDisponiblesOuAffectesNative(?int $projetId, array $idsAExclure = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.projets', 'p', 'WITH', 'p.id = :projetId OR p.id IS NULL')
            ->addSelect('p')
            ->setParameter('projetId', $projetId);

        // Exclure certains employés
        if (!empty($idsAExclure)) {
            $qb->andWhere('e.id NOT IN (:idsAExclure)')
                ->setParameter('idsAExclure', $idsAExclure);
        }

        // Important : on filtre ensuite sur (p.id IS NULL OR p.id = :projetId)
        $qb->andWhere($qb->expr()->orX(
            'p.id IS NULL',
            'p.id = :projetId'
        ));

        $qb->orderBy('e.nom', 'ASC');

        return $qb;
    }
}
