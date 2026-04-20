<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * @return Livre[]
     */
    public function findByCategorie(int $categorieId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.categorie = :cat')
            ->setParameter('cat', $categorieId)
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
