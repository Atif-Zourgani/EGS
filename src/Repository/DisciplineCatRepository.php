<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use App\Entity\DisciplineCat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DisciplineCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisciplineCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisciplineCat[]    findAll()
 * @method DisciplineCat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineCatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DisciplineCat::class);
    }

    public function findCategoryByDisciplineStudent($id)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.disciplines', 'd')
            ->innerJoin('d.disciplineLevels', 'dl')
            ->innerJoin('dl.skills', 's')
            ->innerJoin('s.studentSkills', 'ss')
            ->innerJoin('ss.student', 'st')
            ->where('st.id = :id')
            ->setParameter('id', $id)
            ->distinct()
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findAllCategories()
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.disciplines', 'd')
            ->join('d.disciplineLevels', 'l')
            ->addSelect('d', 'l')
            ->distinct()
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('d.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


    public function findDisciplineCatByStudentSkills($id)
    {

        $disciplines = $this->createQueryBuilder('c')

            ->leftJoin('c.disciplines', 'd')
            ->leftJoin('d.disciplineLevels', 'dl')
            ->leftJoin('dl.skills', 's')
            ->leftJoin('s.disciplineLevel', 'sl')
            ->leftJoin('s.studentSkills', 'ss')
            ->addSelect('d', 'c', 'dl', 's', 'ss', 'sl')
            ->leftJoin('ss.student', 'st')
            ->where('st.id = :id')
            //->andWhere('ss.createdAt BETWEEN :startDate AND :endDate')
            //->setParameter('startDate', $startDate)
            //->setParameter('endDate', $endDate)
            ->setParameter('id', $id)
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('d.name', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $disciplines;

    }

    public function findDisciplineCatByStudentSkillsAndTeacher($id, $teacher, $teacherR)
    {

        $teacherC = $teacherR->findDisciplinesCatByTeacher($teacher);

        $disciplines = $this->createQueryBuilder('c')

            ->leftJoin('c.disciplines', 'd')
            ->leftJoin('d.disciplineLevels', 'dl')
            ->leftJoin('dl.skills', 's')
            ->leftJoin('s.disciplineLevel', 'sl')
            ->leftJoin('s.studentSkills', 'ss')
            ->addSelect('d', 'c', 'dl', 's', 'ss', 'sl')
            ->leftJoin('ss.student', 'st')
            ->where('st.id = :id')
            ->andWhere('c IN(:teacherC)')
            ->setParameter('id', $id)
            ->setParameter('teacherC', $teacherC)
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('d.name', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $disciplines;

    }
}
