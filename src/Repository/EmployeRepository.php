<?php
// src/Repository/EmployeRepository.php
namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class EmployeRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    /**
     * Permet de mettre à jour le mot de passe (quand un utilisateur se reconnecte avec un hash plus récent)
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Employe) {
            throw new \InvalidArgumentException('Instances attendue de Employe');
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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
