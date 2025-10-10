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

    /**
     * Retourne un QueryBuilder pour l'utilisation dans le formulaire (EntityType->query_builder).
     *
     * Comportement :
     *  - si $projetId fourni : on renvoie un QB qui permet d'afficher tous les employés,
     *    y compris ceux déjà affectés à CE projet (utile pour la pré-sélection),
     *    mais la logique de filtrage détaillée pour l'AJAX est gérée côté Ajaxfind...
     *  - si $projetId null : renvoie tous les employés (ordre par nom).
     *
     * IMPORTANT : retourne un QueryBuilder (pas un array).
     */
    public function findEmployesDisponiblesOuAffectes(?int $projetId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.projets', 'p')
            ->addSelect('p');

        if ($projetId) {
            // Inclure les employés non liés à d’autres projets
            // + ceux déjà liés à CE projet
            $qb->andWhere('p.id IS NULL OR p.id = :projetId')
                ->setParameter('projetId', $projetId);
        } else {
            // En mode création : employés non liés à aucun projet
            $qb->andWhere('p.id IS NULL');
        }

        return $qb->orderBy('e.nom', 'ASC');
    }


    /**
     * Méthode utilisée par l'endpoint AJAX (Select2).
     * Implémentée avec QueryBuilder Doctrine mais retourne un array simple
     * de la forme: [ ['id' => ..., 'text' => 'Nom Prenom'], ... ]
     *
     * Comportement : exclut uniquement les employés déjà liés à CE projet
     * (si $projetId fourni). Les employés liés à d'autres projets restent visibles.
     */
    public function AjaxfindEmployesDisponiblesOuAffectes(?int $projetId, ?string $term = null, array $excludeIds = []): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id, e.nom, e.prenom');

        if ($projetId) {
            $qb->leftJoin('e.projets', 'p_with_pid', 'WITH', 'p_with_pid.id = :pid')
                ->setParameter('pid', $projetId);
        }

        if (!empty($term)) {
            $qb->andWhere('e.nom LIKE :term OR e.prenom LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        if (!empty($excludeIds)) {
            $qb->andWhere('e.id NOT IN (:excludeIds)')
                ->setParameter('excludeIds', $excludeIds);
        }

        $qb->orderBy('e.nom', 'ASC')
            ->setMaxResults(50);

        $rows = $qb->getQuery()->getArrayResult();

        return array_map(static function (array $r) {
            return [
                'id' => $r['id'],
                'text' => trim($r['nom'] . ' ' . ($r['prenom'] ?? '')),
            ];
        }, $rows);
    }

}
