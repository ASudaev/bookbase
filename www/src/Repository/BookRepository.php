<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param string $name
     *
     * @return Book[] Returns an array of Book objects
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('b')
            ->addSelect('translation')
            ->leftJoin('b.translations', 'translation')
            ->andWhere('translation.name LIKE :val')
            ->setParameter('val', '%' . $name . '%')
            ->orderBy('translation.name', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return Book|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
