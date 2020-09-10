<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    public function findAllTeachers() {

        return $this->createQueryBuilder('t')
            ->select('t')
            ->join('t.discipline', 'discipline')
            ->addSelect( 'discipline')
            ->getQuery()
            ->getArrayResult();
    }


    public function findByQuery($query) {

        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.discipline', 'd')
            ->innerJoin('d.category', 'c')
            ->where('t.firstname LIKE :query or t.lastname LIKE :query or t.fullname LIKE :query')
            ->orWhere('d.name LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query', '%'.$query.'%');

        return $qb->getQuery()->getResult();
    }

    public function findTeacherByStudentComment($id)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.comments', 'c')
            ->innerJoin('c.student', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->distinct()
            ->getQuery();

        return $qb->getArrayResult();
    }

    public function findDisciplinesByTeacher($id)
    {
        $disciplines = $this->createQueryBuilder('t')
            ->leftJoin('t.discipline', 'd')
            ->select('d.id')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        $disciplinesId = [];

        foreach($disciplines as $discipline) {
            array_push($disciplinesId, $discipline['id']);
        }

        return $disciplinesId;
    }

    public function findDisciplinesCatByTeacher($id)
    {
        $categories = $this->createQueryBuilder('t')
            ->leftJoin('t.discipline', 'd')
            ->leftJoin('d.category', 'c')
            ->select('c.id')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->distinct()
            ->getQuery()
            ->getResult();

        $catId = [];

        foreach($categories as $category) {
            array_push($catId, $category['id']);
        }

        return $catId;
    }
}
