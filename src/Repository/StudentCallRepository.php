<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use App\Entity\StudentCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method StudentCall|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentCall|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentCall[]    findAll()
 * @method StudentCall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentCallRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentCall::class);
    }


    /**
     * Return current year
     */
    public function findCurrentYear()
    {
        return $this->getEntityManager()
            ->getRepository(AcademicYear::class)
            ->createQueryBuilder('y')
            ->where(':date BETWEEN y.startDate AND y.endDate')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Return first day of current's year
     */
    private function firstDayOfYear()
    {

        return $this->findCurrentYear()->getStartDate();
    }


    /**
     * Return last day of current's year
     */
    private function lastDayOfYear()
    {

        return $this->findCurrentYear()->getEndDate();
    }


    /**
     * Return all absences for student's id
     */
    public function findAbsencesByStudent($id)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.rollCall', 'r')
            ->andWhere('c.status = :absent')
            ->andWhere('c.student = :student')
            ->andWhere('r.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $this->firstDayOfYear())
            ->setParameter('endDate', $this->lastDayOfYear())
            ->setParameter('absent', 'absent')
            ->setParameter('student', $id)
            ->getQuery()
            ->getResult();
    }


    /**
     * Return all delays for student's id
     */
    public function findDelaysByStudent($id)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.rollCall', 'r')
            ->andWhere('c.status = :late')
            ->andWhere('c.student = :student')
            ->andWhere('r.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $this->firstDayOfYear())
            ->setParameter('endDate', $this->lastDayOfYear())
            ->setParameter('late', 'late')
            ->setParameter('student', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return all absences and delays for student's id
     */
    public function findAbsencesAndDelaysByStudent($id)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.rollCall', 'r')
            ->andWhere('c.status = :absent')
            ->orWhere('c.status = :late')
            ->orWhere('c.status = :justified')
            ->andWhere('c.student = :student')
            ->andWhere('r.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $this->firstDayOfYear())
            ->setParameter('endDate', $this->lastDayOfYear())
            ->setParameter('absent', 'absent')
            ->setParameter('late', 'late')
            ->setParameter('justified', 'justified')
            ->setParameter('student', $id)
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Return all absences by section (not utilised)
     */
    public function findAbsencesBySection()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.rollCall', 'r')
            ->leftJoin('c.student', 'stu')
            ->leftJoin('stu.section', 'sec')
            ->leftJoin('sec.grade', 'g')
            ->addSelect('COUNT(c) as absences', 'sec.name', 'g.name')
            ->andWhere('c.status = :absent')
            ->andWhere('r.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('unjustified', 'unjustified')
            ->setParameter('startDate', $this->firstDayOfYear())
            ->setParameter('endDate', $this->lastDayOfYear())
            ->setParameter('absent', 'absent')
            ->groupBy('stu.section')
            ->getQuery()
            ->getResult();
    }


    /**
     * Return last roll call date for the selected section
     */
    public function findLastRollCallDateBy($section)
    {
        $lastRollCallDate = $this->createQueryBuilder('c')
            ->leftJoin('c.rollCall', 'r')
            ->select('r.createdAt')
            ->setMaxResults(1)
            ->orderBy('r.createdAt', 'DESC')
            ->leftJoin('c.student', 'stu')
            ->leftJoin('stu.section', 'sec')
            ->andWhere('sec.id = :section')
            ->setParameter('section', $section)
            ->getQuery()
            ->getSingleResult();

        $date = $lastRollCallDate['createdAt'];

        return $date;
    }


    /**
     * Return first day of week for the last roll call
     */
    public function firstDayOfWeek($section)
    {
        return date_modify($this->findLastRollCallDateBy($section),'last sunday +1 day')->format('Y-m-d');
    }


    /**
     * Return last day of week for the last roll call
     */
    public function lastDayOfWeek($section)
    {
        return date_modify($this->findLastRollCallDateBy($section),'last sunday +7 day')->format('Y-m-d');
    }

    /**
     * Return first day of week for the second to last roll call
     */
    public function firstDayOfLastWeek($section)
    {
        return date_modify($this->findLastRollCallDateBy($section),'last sunday -6 day')->format('Y-m-d');
    }


    /**
     * Return last day of week for the second to last roll call
     */
    public function lastDayOfLastWeek($section)
    {
        return date_modify($this->findLastRollCallDateBy($section),'last sunday -2 day')->format('Y-m-d');
    }

    /**
     * Find all roll calls to download
     */
    public function findAllRollCallsOfTheYear()
    {
        return $this->createQueryBuilder('c')
            ->join('c.rollCall', 'r')
            ->join('c.student', 'stu')
            ->join('stu.section', 'sec')
            ->addSelect('sec.name as section', 'r.createdAt', 'r.halfDay', 'stu.lastname', 'stu.firstname', 'c.status')
            ->andWhere('c.status = :absent')
            ->orWhere('c.status = :late')
            ->orWhere('c.status = :justified')
            ->setParameter('absent', 'absent')
            ->setParameter('late', 'late')
            ->setParameter('justified', 'justified')
            ->orderBy('sec.name', 'ASC')
            ->addOrderBy('stu.lastname')
            ->addOrderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

}
