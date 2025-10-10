<?php

namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employe>
 */
class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    /**
     * Retourne les employés non affectés à d'autres projets
     * ou déjà liés à un projet donné.
     * Voici la requete construite par Doctrine :
     *
     * SELECT e.*, p.*
     * FROM employe e
     * LEFT JOIN employe_projet ep ON ep.employe_id = e.id
     * LEFT JOIN projet p ON p.id = ep.projet_id
     * WHERE p.id IS NULL OR p.id = :pid
     */
    public function findEmployesDisponiblesOuAffectes(?int $projetId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.projets', 'p')
            ->addSelect('p');

        if ($projetId) {
            $qb->where('p.id IS NULL OR p.id = :pid')
                ->setParameter('pid', $projetId);
        } else {
            $qb->where('p.id IS NULL');
        }

        return $qb;
    }

    // Version SQL optimisée pour AJAX / Select2
    public function AjaxfindEmployesDisponiblesOuAffectes(?int $projetId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT e.id, e.nom
            FROM employe e
            WHERE e.id NOT IN (
                SELECT ep.employe_id
                FROM employe_projet ep
                WHERE ep.projet_id <> :pid
            )
            OR e.id IN (
                SELECT ep.employe_id
                FROM employe_projet ep
                WHERE ep.projet_id = :pid
            )
            ORDER BY e.nom ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('pid', $projetId ?? 0);
        return $stmt->executeQuery()->fetchAllAssociative();
    }
}
