<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param string $name
     *
     * @return Author[] Returns an array of Author objects
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name LIKE :val')
            ->setParameter('val', '%' . $name . '%')
            ->orderBy('a.name', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     *
     * @return Author|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByNameStrict(string $name): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name LIKE :val')
            ->setParameter('val', $name)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     *
     * @return Author|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string[] $ids
     *
     * @return array
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id IN (:val)')
            ->setParameter('val', $ids)
            ->getQuery()
            ->getResult();
    }
}
