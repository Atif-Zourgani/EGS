<?php

namespace App\Repository;

use App\Entity\RollCall;
use App\Entity\StudentCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RollCall|null find($id, $lockMode = null, $lockVersion = null)
 * @method RollCall|null findOneBy(array $criteria, array $orderBy = null)
 * @method RollCall[]    findAll()
 * @method RollCall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RollCallRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RollCall::class);
    }

    public function displayLastCalls()
    {
        return $this->createQueryBuilder('r')
                    ->leftJoin('r.section', 's')
                    ->setMaxResults(500)
                    ->orderBy('r.createdAt', 'DESC')
                    ->addOrderBy('s.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function displayTeacherCalls($teacher)
    {
        return $this->createQueryBuilder('r')
                    ->leftJoin('r.section', 's')
                    ->andWhere('r.teacher = :teacher')
                    ->andWhere('r.createdAt = :date')
                    ->setParameter('teacher', $teacher)
                    ->setParameter('date', date('Y-m-d'))
                    ->orderBy('r.createdAt', 'DESC')
                    ->addOrderBy('s.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function displayDateCall()
    {
        return $this->createQueryBuilder('r')
            ->groupBy('r.createdAt')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Return attendance's count by section for the last roll call's date
     */
    public function findAttendanceBySection($section, $week)
    {
        $studentCallR = $this->getEntityManager()->getRepository(StudentCall::class);

        // if last week or second to last week
        if ($week == 1) {
            $firstDayOfWeek = $studentCallR->firstDayOfWeek($section);
            $lastDayOfWeek = $studentCallR->lastDayOfWeek($section);
        } elseif ($week == 2) {
            $firstDayOfWeek = $studentCallR->firstDayOfLastWeek($section);
            $lastDayOfWeek = $studentCallR->lastDayOfLastWeek($section);
        }

        return $this->createQueryBuilder('r')
            ->leftJoin('r.section', 's')
            ->leftJoin('r.studentCalls', 'sc')
            ->select('COUNT(sc) as attendance', 's.name')
            ->andWhere('sc.status = :present')
            ->orWhere('sc.status = :late')
            ->orWhere('sc.status = :justified')
            ->andWhere('r.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('s = :section')
            ->setParameter('startDate', $firstDayOfWeek)
            ->setParameter('endDate', $lastDayOfWeek)
            ->setParameter('present', 'present')
            ->setParameter('late', 'late')
            ->setParameter('justified', 'justified')
            ->setParameter('section', $section)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getSingleResult();
    }


    /**
     * Return roll call's count by section for the last roll call's date
     */
    public function findAllRollCallBySection($section, $week)
    {
        $studentCallR = $this->getEntityManager()->getRepository(StudentCall::class);

        // if last week or second to last week
        if ($week == 1) {
            $firstDayOfWeek = $studentCallR->firstDayOfWeek($section);
            $lastDayOfWeek = $studentCallR->lastDayOfWeek($section);
        } elseif ($week == 2) {
            $firstDayOfWeek = $studentCallR->firstDayOfLastWeek($section);
            $lastDayOfWeek = $studentCallR->lastDayOfLastWeek($section);
        }

        return $this->createQueryBuilder('r')
            ->innerJoin('r.studentCalls', 'c')
            ->leftJoin('r.section', 's')
            ->select('count(c) as calls')
            ->andWhere('r.section = :section')
            ->andWhere('r.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('section', $section)
            ->setParameter('startDate', $firstDayOfWeek)
            ->setParameter('endDate', $lastDayOfWeek)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getSingleResult();
    }


}
