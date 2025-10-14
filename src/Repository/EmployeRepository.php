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
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT e0_.id
        FROM employe e0_
                 LEFT JOIN employe_projet e2_
                        ON e0_.id = e2_.employe_id AND e2_.projet_id = :projetId
                 LEFT JOIN projet p1_ ON p1_.id = e2_.projet_id
        WHERE (p1_.id IS NULL OR p1_.id = :projetId)
    ';


        // Si $projetId est défini, on ajoute la condition
        if ($projetId !== null) {
            $sql .= ' AND (p1_.id IS NULL OR p1_.id = :projetId)';
        }

        if (!empty($idsAExclure)) {
            $sql .= ' AND e0_.id NOT IN (:idsAExclure)';
        }

        $sql .= ' ORDER BY e0_.nom ASC';

        $params = ['projetId' => $projetId];
        $types = [];

        if (!empty($idsAExclure)) {
            $params['idsAExclure'] = $idsAExclure;
            $types['idsAExclure'] = Types::INTEGER;
        }

        // Exécution de la requête SQL native
        $stmt = $conn->prepare($sql);
        $ids = $stmt->executeQuery($params, $types)->fetchFirstColumn();

        // si aucun id trouvé, on met un id impossible pour éviter IN ()
        $ids = !empty($ids) ? $ids : [0];

        // Retourner un QueryBuilder, pas le résultat
        $qb = $this->createQueryBuilder('e')
            ->where('e.id IN (:ids)')
            ->setParameter('ids', $ids) // optionnel : ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->orderBy('e.nom', 'ASC');

        return $qb;
    }


    public function ajaxDispo(Request $request, EmployeRepository $employeRepo): JsonResponse
    {
        $projetId = (int) $request->query->get('projet', 0);
        $idsAExclure = $request->query->get('exclude', []); // tableau d'IDs à exclure

        // On récupère les employés via la requête SQL native
        $qb = $employeRepo->findEmployesDisponiblesOuAffectesNative($projetId, $idsAExclure);
        $employes = $qb->getQuery()->getResult(); // renvoie un tableau d'objets Employe

        // On formate le JSON pour Select2
        $results = array_map(fn($e) => [
            'id' => $e->getId(),
            'text' => $e->getNom() . ' ' . $e->getPrenom()
        ], $employes);

        return $this->json(['results' => $results]);
    }
}
