<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Proxies\__CG__\App\Entity\Semester;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function findAllStudents() {

        return $this->createQueryBuilder('s')
            ->andWhere('s.enabled = :enabled')
            ->innerJoin('s.section', 'section')
            ->addSelect('section')
            ->setParameter('enabled', 1)
            ->getQuery()
            ->getResult();
    }

    public function findStudentBy($id) {

       return $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->leftJoin('s.section', 'sc')
            ->addSelect('sc')
            ->leftJoin('s.reliability', 'r')
            ->leftJoin('r.incident', 'i')
            ->addSelect('sc', 'r', 'i')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findStudentBySection($sectionName) {

        return $this->createQueryBuilder('s')
            ->innerJoin('s.section', 'sc')
            ->where('sc.shortname = :sectionName')
            ->andWhere('s.enabled = :enabled')
            ->setParameter('sectionName', $sectionName)
            ->setParameter('enabled', 1)
            ->getQuery()
            ->getResult();
    }

    public function countStudentsBySection() {

        return $this->createQueryBuilder('s')
            ->select('count(s) as studentNumber')
            ->where('s.enabled = :enabled')
            ->setParameter('enabled', 1)
            ->groupBy('s.section')
            ->getQuery()
            ->getResult();
    }

    public function findByQuery($query) {

        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.firstname LIKE :query or s.lastname LIKE :query or s.fullname LIKE :query')
            ->andWhere('s.enabled = :enabled')
            ->setParameter('query', '%'.$query.'%')
            ->setParameter('enabled', 1)
            ->getQuery()
            ->getResult();
    }

    public function findElite() {

        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.elite = :elite')
            ->andWhere('s.enabled = :enabled')
            ->setParameter('elite', 1)
            ->setParameter('enabled', 1)
            ->getQuery()
            ->getResult();
    }

    public function findChallenger() {

        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.challenger = :challenger')
            ->andWhere('s.enabled = :enabled')
            ->setParameter('challenger', 1)
            ->setParameter('enabled', 1)
            ->getQuery()
            ->getResult();
    }

    public function findGamer() {

        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.challenger IS NULL')
            ->andWhere('s.elite IS NULL')
            ->andWhere('s.enabled = :enabled')
            ->setParameter('enabled', 1)
            ->getQuery()
            ->getResult();
    }

    /*
    * Find all students with reliability points => 20 in current's semester
    */
    public function findStudentByBestReliabilityPoints($semesterId, $points)
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

        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.reliability', 'r')
            ->leftJoin('r.incident', 'i')
            ->join('s.section', 'sec')
            ->select('s.id', 's.firstname', 's.lastname', 's.image')
            ->addSelect('SUM(i.points) + 20 as points')
            ->having('SUM(i.points) + 20 >= :points')
            ->orHaving("SUM(i.points) IS NULL")
            ->andWhere('s.enabled = :enabled')
            ->andWhere('r.createdAt >= :startDate')
            ->andWhere('r.createdAt <= :endDate')
            ->orWhere('r.createdAt IS NULL')
            ->setParameter('points', $points)
            ->setParameter('enabled', 1)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('s')
            ->orderBy('SUM(i.points)', 'DESC')
            ->addOrderBy('sec.id', 'ASC')
            ->addOrderBy('s.lastname', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $qb;

    }

    /*
    * Find all students with reliability points => 20 in current's semester
    */
    public function findStudentByBadReliabilityPoints($semesterId, $points)
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

        $qb = $this->createQueryBuilder('s')
            ->join('s.reliability', 'r')
            ->join('r.incident', 'i')
            ->select('s.id', 's.firstname', 's.lastname', 's.image')
            ->addSelect('SUM(i.points) + 20 as points')
            ->having('SUM(i.points) + 20 <= :points')
            ->andWhere('s.enabled = :enabled')
            ->andWhere('r.createdAt >= :startDate')
            ->andWhere('r.createdAt <= :endDate')
            ->orWhere('r.createdAt IS NULL')
            ->setParameter('points', $points)
            ->setParameter('enabled', 1)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('s')
            ->orderBy('SUM(i.points)', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $qb;

    }

    /**
     * Find all students without user's account
     *
     * @return array
     */
    public function findStudentsWithoutAccount()
    {
        return $this->createQueryBuilder('s')
            ->andWhere("s.user is null")
            ->leftJoin('s.section', 'sec')
            ->addSelect('sec')
            ->orderBy('sec.name', 'ASC')
            ->addOrderBy('s.lastname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
