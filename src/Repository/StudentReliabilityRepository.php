<?php

namespace App\Repository;

use App\Entity\StudentReliability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StudentReliability|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentReliability|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentReliability[]    findAll()
 * @method StudentReliability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentReliabilityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentReliability::class);
    }

    public function reliabilityFindBy($id)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r')
            ->innerJoin('r.teacher', 't')
            ->addSelect('t')
            ->innerJoin('r.team', 'te')
            ->addSelect('te')
            ->innerJoin('r.student', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->getArrayResult();
    }

    public function reliabilityDateBy($id)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.createdAt')
            ->innerJoin('r.student', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->getArrayResult();
    }

    public function findLastStudentIncident($student, $incident)
    {
        return $this->createQueryBuilder('r')
            ->join('r.student', 's')
            ->join('r.incident', 'i')
            ->where('s.id = :student')
            ->andWhere('i.id = :incident')
            ->addSelect('i')
            ->setParameter('student', $student)
            ->setParameter('incident', $incident)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all student's incidents to download
     */
    public function findAllIncidentsToDownload()
    {
        return $this->createQueryBuilder('r')
            ->join('r.incident', 'i')
            ->join('r.student', 'stu')
            ->join('stu.section', 'sec')
            //->addSelect('i.name as incidentName', 'i.points as incidentPoints', 'sec.name as section', 'r.createdAt', 'stu.lastname', 'stu.firstname')
            //->addSelect('sec.name as section', 'stu.lastname', 'stu.firstname')
            ->addSelect('stu', 'sec', 'i')
            ->orderBy('sec.name', 'ASC')
            ->addOrderBy('stu.lastname')
            ->addOrderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
