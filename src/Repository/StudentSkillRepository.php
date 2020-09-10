<?php

namespace App\Repository;

use App\Entity\StudentSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StudentSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentSkill[]    findAll()
 * @method StudentSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentSkillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentSkill::class);
    }

    public function findByStudentAndLevel($student, $level)
    {
        return $this->createQueryBuilder('sk')
            ->select('count(sk) as studentSkills')
            ->join('sk.student', 'st')
            ->join('sk.skills', 's')
            ->where('st = :student')
            ->andWhere('s.disciplineLevel = :level')
            ->setParameter('student', $student)
            ->setParameter('level', $level)
            ->getQuery()
            ->getSingleResult();
    }


    public function countSkillsByStudentAndPathway($student, $pathway, $grade)
    {
        return $this->createQueryBuilder('sk')
            ->select('count(sk) as studentSkills')
            ->join('sk.student', 'st')
            ->join('sk.skills', 's')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->where('st = :student')
            ->andWhere('c.pathway = :pathway')
            ->andWhere('c.grade = :grade')
            ->setParameter('student', $student)
            ->setParameter('pathway', $pathway)
            ->setParameter('grade', $grade)
            ->getQuery()
            ->getSingleResult();
    }

    public function countSkillsByStudentAndCareer($student, $career)
    {
        return $this->createQueryBuilder('sk')
            ->select('count(sk) as studentSkills')
            ->join('sk.student', 'st')
            ->join('sk.skills', 's')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->where('st = :student')
            ->andWhere('c.id = :career')
            ->setParameter('student', $student)
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();
    }

    public function countSkillsByStudentAndProfession($student, $profession)
    {
       return $this->createQueryBuilder('sk')
            ->select('count(sk) as studentSkills')
            ->join('sk.student', 'st')
            ->join('sk.skills', 's')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->join('c.profession', 'p')
            ->where('st = :student')
            ->andWhere('p.id = :profession')
            ->setParameter('student', $student)
            ->setParameter('profession', $profession)
            ->getQuery()
            ->getSingleResult();
    }

}
