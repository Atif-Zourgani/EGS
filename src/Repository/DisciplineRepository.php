<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use App\Entity\Discipline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Discipline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discipline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discipline[]    findAll()
 * @method Discipline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    public function findAllDisciplines() {

        return $this->createQueryBuilder('d')
            ->select('d.name', 'd.image')
            ->getQuery()
            ->getResult();
    }

    public function findAllAsc() {

        return $this->createQueryBuilder('d')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllNames() {

        return $this->createQueryBuilder('d')
            ->select('d.name')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDisciplineBy($discipline)
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.disciplineLevels', 'l')
            ->leftJoin('d.category', 'c')
            ->leftJoin('l.level', 'll')
            ->leftJoin('l.skills', 's')
            ->leftJoin('l.exercises', 'e')
            ->addSelect('l', 'c', 'e', 's', 'll')
            ->where('d.name = :discipline')
            ->setParameter('discipline', $discipline)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /*public function findDisciplineByStudentComment($id)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->innerJoin('d.comments', 'c')
            ->innerJoin('c.student', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->distinct()
            //->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }*/

    public function findDisciplineByStudentSkills($id)
    {

        $disciplines = $this->createQueryBuilder('d')

            ->leftJoin('d.category', 'c')
            ->leftJoin('d.disciplineLevels', 'dl')
            ->leftJoin('dl.skills', 's')
            ->leftJoin('s.disciplineLevel', 'sl')
            ->leftJoin('s.studentSkills', 'ss')
            ->addSelect('c', 'dl', 's', 'ss', 'sl')
            ->leftJoin('ss.student', 'st')
            ->where('st.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        return $disciplines;

    }

    public function findDisciplineByStudentSkillsAndTeacher($id, $teacher, $teacherR)
    {

        $teacherD = $teacherR->findDisciplinesByTeacher($teacher);

        $disciplines = $this->createQueryBuilder('d')

            ->leftJoin('d.category', 'c')
            ->leftJoin('d.teachers', 't')
            ->leftJoin('d.disciplineLevels', 'dl')
            ->leftJoin('dl.skills', 's')
            ->leftJoin('s.disciplineLevel', 'sl')
            ->leftJoin('s.studentSkills', 'ss')
            ->addSelect('c', 'dl', 's', 'ss', 'sl', 't')
            ->leftJoin('ss.student', 'st')
            ->leftJoin('ss.teacher', 'stt')
            ->where('st.id = :id')
            ->andWhere('d IN(:teacherD)')
            ->setParameter('id', $id)
            ->setParameter('teacherD', $teacherD)
            ->getQuery()
            ->getArrayResult();

        return $disciplines;

    }

    public function findByTeacher($teacher, $teacherR)
    {
        $teacherD = $teacherR->findDisciplinesByTeacher($teacher);

        return $this->createQueryBuilder('d')

            ->leftJoin('d.category', 'c')
            ->leftJoin('d.teachers', 't')
            ->addSelect('c', 't')
            ->andWhere('d IN(:teacherD)')
            ->setParameter('teacherD', $teacherD)
            ->getQuery()
            ->getArrayResult();
    }

    public function findNamesByTeacher($teacher, $teacherR)
    {
        $teacherD = $teacherR->findDisciplinesByTeacher($teacher);

        return $this->createQueryBuilder('d')

            ->leftJoin('d.category', 'c')
            ->leftJoin('d.teachers', 't')
            ->select('d.name')
            ->andWhere('d IN(:teacherD)')
            ->setParameter('teacherD', $teacherD)
            ->distinct()
            ->getQuery()
            ->getArrayResult();
    }



    public function findByQuery($query) {

        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->innerJoin('d.category', 'c')
            ->addSelect('c')
            ->where('d.name LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query', '%'.$query.'%');

        return $qb->getQuery()->getResult();
    }


    public function findDisciplineByCareer($pathway, $grade)
    {
        return $this->createQueryBuilder('d')
            ->join('d.disciplineLevels', 'dl')
            ->join('dl.academicCareers', 'c')
            ->join('c.grade', 'g')
            ->join('c.pathway', 'p')
            ->join('dl.skills', 's')
            ->join('s.studentSkills', 'sk')
            ->join('sk.student', 'st')
            ->andWhere('p.name = :pathway')
            ->andWhere('g.name = :grade')
            ->addSelect('dl', 'g', 'c', 's', 'sk', 'st')
            ->setParameter('pathway', $pathway)
            ->setParameter('grade', $grade)
            ->getQuery()
            ->getArrayResult();
    }


    public function findDisciplineBySpecialism($pathway, $grade, $specialism)
    {
        return $this->createQueryBuilder('d')
            ->join('d.disciplineLevels', 'dl')
            ->join('dl.academicCareers', 'c')
            ->join('c.grade', 'g')
            ->join('c.pathway', 'p')
            ->join('c.specialism', 'spe')
            ->join('dl.skills', 's')
            ->join('s.studentSkills', 'sk')
            ->join('sk.student', 'st')
            ->andWhere('p.name = :pathway')
            ->andWhere('g.name = :grade')
            ->andWhere('spe.id = :spe')
            ->addSelect('dl', 'g', 'c', 's', 'sk', 'st', 'spe')
            ->setParameter('pathway', $pathway)
            ->setParameter('grade', $grade)
            ->setParameter('spe', $specialism)
            ->getQuery()
            ->getArrayResult();
    }


    public function findDisciplinesByCareer($career)
    {
        return $this->createQueryBuilder('d')
            ->join('d.disciplineLevels', 'dl')
            ->join('dl.academicCareers', 'c')
            //->join('c.id', 'career')
            //->join('c.pathway', 'p')
            //->join('c.specialism', 'spe')
            ->join('dl.skills', 's')
            //->join('s.studentSkills', 'sk')
            //->join('sk.student', 'st')
            ->andWhere('c.id = :career')
            //->andWhere('g.name = :grade')
            //->andWhere('spe.id = :spe')
            ->addSelect('dl', 'c', 's')
            //->setParameter('pathway', $pathway)
            //->setParameter('grade', $grade)
            ->setParameter('career', $career)
            ->getQuery()
            ->getArrayResult();
    }


    public function findDisciplineByProfession($pro)
    {
        return $this->createQueryBuilder('d')
            ->join('d.disciplineLevels', 'dl')
            ->join('dl.academicCareers', 'c')
            ->join('c.profession', 'p')
            ->join('dl.skills', 's')
            ->andWhere('p.name = :pro')
            ->addSelect('dl', 'c', 's', 'p')
            ->setParameter('pro', $pro)
            ->getQuery()
            ->getArrayResult();
    }


    public function findDisciplineByProfessionForStudent($pro)
    {
        return $this->createQueryBuilder('d')
            ->join('d.disciplineLevels', 'dl')
            ->join('dl.academicCareers', 'c')
            ->join('c.profession', 'p')
            ->join('dl.skills', 's')
            ->join('s.studentSkills', 'sk')
            ->join('sk.student', 'st')
            ->andWhere('p.name = :pro')
            ->addSelect('dl', 'c', 's', 'sk', 'st', 'p')
            ->setParameter('pro', $pro)
            ->getQuery()
            ->getArrayResult();
    }


    public function findDisciplineAndSkillsByCareer($careerEntity, $discipline)
    {
        return $this->createQueryBuilder('d')
            ->where('d.id = :discipline')
            ->join('d.disciplineLevels', 'dl')
            ->innerJoin('dl.academicCareers', 'ac')
            ->leftJoin('d.category', 'c')
            ->leftJoin('dl.level', 'l')
            ->leftJoin('dl.skills', 's')
            ->leftJoin('dl.exercises', 'e')
            ->addSelect('l', 'c', 'e', 's', 'dl', 'ac')
            ->andWhere('ac.id = :career')
            ->setParameter('discipline', $discipline)
            ->setParameter('career', $careerEntity)
            ->getQuery()
            ->getSingleResult();
    }

    // en test
    /*public function findDisciplineAndSkillsByCareerAndStudent($career, $student)
    {
        return $this->createQueryBuilder('d')
            ->join('d.disciplineLevels', 'dl')
            ->innerJoin('dl.academicCareers', 'ac')
            ->leftJoin('d.category', 'c')
            ->leftJoin('dl.level', 'l')
            ->leftJoin('dl.skills', 's')
            ->leftJoin('dl.exercises', 'e')
            ->leftJoin('s.disciplineLevel', 'sl')
            ->leftJoin('s.studentSkills', 'ss')
            ->innerJoin('ss.student', 'st')
            ->addSelect('count(ss) as test')
            ->addSelect('l', 'c', 'e', 's', 'dl', 'ac', 'sl', 'ss', 'st')
            ->andWhere('ac.id = :career')
            ->andWhere('st.id = :student')
            ->setParameter('student', $student)
            ->setParameter('career', $career)
            ->getQuery()
            ->getArrayResult();
    }*/

    public function findByName($discipline)
    {
        return $this->createQueryBuilder('d')
            ->select('d.id')
            ->where('d.name LIKE :discipline')
            ->setParameter('discipline', '%' .$discipline . '%')
            ->getQuery()
            ->getOneOrNullResult();
    }

}
