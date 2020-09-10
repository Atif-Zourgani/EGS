<?php

namespace App\Repository;

use App\Entity\StudentParent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StudentParent|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentParent|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentParent[]    findAll()
 * @method StudentParent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentParentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentParent::class);
    }

    public function findAllParents() {

        return $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.student', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();
    }


    public function findByQuery($query) {

        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.student', 's')
            ->where('p.firstname LIKE :query or p.lastname LIKE :query or p.fullname LIKE :query')
            ->orWhere('s.firstname LIKE :query or s.lastname LIKE :query or s.fullname LIKE :query')
            ->setParameter('query', '%'.$query.'%');

        return $qb->getQuery()->getResult();
    }

}
