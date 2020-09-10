<?php

namespace App\Repository;

use App\Entity\Semester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Semester|null find($id, $lockMode = null, $lockVersion = null)
 * @method Semester|null findOneBy(array $criteria, array $orderBy = null)
 * @method Semester[]    findAll()
 * @method Semester[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SemesterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Semester::class);
    }

    public function findCurrentSemester()
    {
        $currentSemester = $this->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->where(':date BETWEEN s.startDate AND s.endDate')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->getSingleResult();

        $firstSemester = $this->createQueryBuilder('s')
            ->select('MIN(s.id)')
            ->getQuery()
            ->getSingleResult();

        return $currentSemester;
    }

    public function findLastsSemesters() {
        return $this->createQueryBuilder('s')
            ->where('s.startDate < :date')
            ->setParameter('date', date('Y-m-d'))
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
