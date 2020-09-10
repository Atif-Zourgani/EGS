<?php

namespace App\Repository;

use App\Entity\Incident;
use App\Entity\Semester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Incident|null find($id, $lockMode = null, $lockVersion = null)
 * @method Incident|null findOneBy(array $criteria, array $orderBy = null)
 * @method Incident[]    findAll()
 * @method Incident[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncidentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Incident::class);
    }


    /*
     * Find all student's incidents for current's semester
     */
    public function findIncidentBy($id, $semesterId)
    {
        $semester = $this->getEntityManager()
            ->getRepository(Semester::class)
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $semesterId)
            ->getQuery()
            ->getOneOrNullResult();

        $startDate = $semester->getStartDate();
        $endDate = $semester->getEndDate();

        return $this->createQueryBuilder('i')
            ->addSelect('i.name as name', 'i.points as points')
            ->innerJoin('i.studentReliability', 'r')
            ->innerJoin('r.student', 's')
            ->leftJoin('r.teacher', 'tr')
            ->leftJoin('r.team', 'tm')
            ->addSelect('tr.fullname as teacher', 'tm.fullname as team')
            ->addSelect("DATE_FORMAT(r.createdAt, '%d/%m') as date"/*, 'SUM(i.points) as points'*/)
            ->where('s.id = :id')
            ->andWhere('r.createdAt >= :startDate')
            ->andWhere('r.createdAt <= :endDate')
            ->orderBy('r.createdAt', 'ASC')
            /*->groupBy('r.createdAt')*/
            ->setParameter('id', $id)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getArrayResult();
    }


    /*
     * Find incident's points by student and current's semester
     */
    public function incidentPointsBy($id, $semesterId)
    {
        $semester = $this->getEntityManager()
            ->getRepository(Semester::class)
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $semesterId)
            ->getQuery()
            ->getOneOrNullResult();

        $startDate = $semester->getStartDate();
        $endDate = $semester->getEndDate();

        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.points) as points')
            ->innerJoin('i.studentReliability', 'r')
            ->innerJoin('r.student', 's')
            ->where('s.id = :id')
            ->andWhere('r.createdAt >= :startDate')
            ->andWhere('r.createdAt <= :endDate')
            ->groupBy('r.createdAt')
            ->setParameter('id', $id)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery();

        return $qb->getResult();
    }


    // Find incident by type (negative or positive)
    public function findByType($type)
    {
        return $this->createQueryBuilder('i')
            ->where('i.incidentType = :type')
            ->setParameter('type', $type);
    }
}
