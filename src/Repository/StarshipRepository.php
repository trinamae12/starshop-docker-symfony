<?php

namespace App\Repository;

use App\Entity\Starship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    /**
     * Get paginated users
     * 
     * @param int $page  The current page (-1 based)
     * @param int $limit Number of results per page
     * 
     * @return Paginator
     */
    public function findPaginatedStarships(int $page, int $limit): Paginator 
    {
        $query = $this->createQueryBuilder('ships')
            ->orderBy('ships.id', 'ASC')
            ->getQuery(); // Returns query object
        
        $query->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        //dd(var_dump($query));

        return new Paginator($query);
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

    // Delete a starship
    public function deleteStarship(Starship $starship): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($starship);
        $entityManager->flush();
    }

    /**
     * Search a starship
     * 
     */
    public function findStarshipBySearchQuery(string $query, int $limit, int $offset): Paginator
    {
        $results = $this->createQueryBuilder('s')
            ->where('s.name LIKE :query')
            ->setParameter('query', '%' .$query. '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();
            // ->getResult();
        
        return new Paginator($results);
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
