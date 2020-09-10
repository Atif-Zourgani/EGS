<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AcademicYear|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcademicYear|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcademicYear[]    findAll()
 * @method AcademicYear[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademicYearRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AcademicYear::class);
    }

    public function findCurrentYear()
    {
        $currentYear = $this->createQueryBuilder('y')
            ->select('MAX(y.id)')
            //->select('y.id')
            ->where(':date BETWEEN y.startDate AND y.endDate')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->getSingleResult();

        return $currentYear;
    }

    public function findLastsYears() {
        return $this->createQueryBuilder('y')
            ->where('y.startDate < :date')
            ->setParameter('date', date('Y-m-d'))
            ->orderBy('y.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

