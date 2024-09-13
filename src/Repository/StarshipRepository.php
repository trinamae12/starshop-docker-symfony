<?php

namespace App\Repository;

use App\Entity\Starship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Starship>
 */
class StarshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Starship::class);
    }

    public function createStarship(Starship $starship)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($starship);
        $entityManager->flush();
    }

    // Find all users
    public function findAllStarships(): array
    {
        return $this->findAll();
    }

    public function findStarship($id)
    {
        return $this->find($id);
    }

    public function updateStarship(Starship $starship)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($starship);
        $entityManager->flush();
    }

    // Delete a user
    public function deleteStarship(Starship $starship): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($starship);
        $entityManager->flush();
    }

    //    /**
    //     * @return Starship[] Returns an array of Starship objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
}
