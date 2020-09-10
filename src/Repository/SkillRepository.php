<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    public function sumAllSkills($discipline)
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $discipline)
            ->getQuery()
            ->getResult();
    }

    public function sumAllSkillsByCareer($discipline, $career)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s) as total')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->join('l.discipline', 'd')
            ->where('d.id = :discipline')
            ->andWhere('c.id = :career')
            ->setParameter('discipline', $discipline)
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();
    }

    // COUNT ALL SKILLS IN A DISCIPLINE FOR A STUDENT, GROUPED BY LEVEL (count student' skills + level.id) // UNUSED
    /*public function countStudentSkills($student) {

        return $this->createQueryBuilder('s')
            ->select('COUNT(s) as skillsNbr')
            ->leftJoin('s.studentSkills', 'ss')
            ->join('s.disciplineLevel', 'l')
            ->join('l.level', 'le')
            ->join('l.discipline', 'd')
            ->where('ss.student = :student')
            ->setParameter('student', $student)
            ->groupBy('l')
            ->addSelect('l.id', 'd.name', 'd.image')
            ->getQuery()
            ->getResult();
    }*/

    // FIND LAST LEVEL FOR STUDENT AND DISCIPLINE
    public function findLastDisciplineLevel($discipline)
    {
        return $this->createQueryBuilder('s')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->addSelect('l')
            ->andWhere('d.name = :name')
            ->setParameter('name', $discipline)
            ->getQuery()
            ->getResult();
    }

    // FIND CURRENT LEVEL FOR STUDENT AND DISCIPLINE
    public function findByDiscipline($discipline, $student)
    {
        // COUNT ALL SKILLS IN A DISCIPLINE, GROUP BY LEVEL (count skills + level.id)
        $countSkillsLevel = $this->createQueryBuilder('s')
            ->select('COUNT(s) as skillsNbr')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->andWhere('d.name = :name')
            ->setParameter('name', $discipline)
            ->groupBy('l')
            ->addSelect('l.id')
            ->getQuery()
            ->getResult();

        // COUNT ALL SKILLS IN A DISCIPLINE FOR A STUDENT, GROUPED BY LEVEL (count student' skills + level.id)
        $countStudentSkillsLevel = $this->createQueryBuilder('s')
            ->select('COUNT(s) as skillsNbr')
            ->leftJoin('s.studentSkills', 'ss')
            ->join('s.disciplineLevel', 'l')
            ->join('l.level', 'le')
            ->join('l.discipline', 'd')
            ->where('ss.student = :student')
            ->andWhere('d.name = :name')
            ->setParameter('name', $discipline)
            ->setParameter('student', $student)
            ->groupBy('l')
            ->addSelect('l.id')
            ->getQuery()
            ->getResult();


        // COUNT SKILLS TO ASSIGNED GROUPED BY LEVEL            // UNUSED //
        /*$countNullSkills = $this->createQueryBuilder('s')
            ->select('COUNT(s) as skillsNbr')
            ->leftJoin('s.studentSkills', 'ss')
            ->join('s.disciplineLevel', 'l')
            ->join('l.level', 'le')
            ->join('l.discipline', 'd')
            ->andWhere('d.name = :name')
            ->andWhere('ss.student != :student')
            ->setParameter('name', $discipline)
            ->setParameter('student', $student)
            ->groupBy('l')
            ->addSelect('l.id')
            ->getQuery()
            ->getResult();*/


        // ARRAY OF ALL COMPLETE DISCIPLINE'S LEVELS FOR A STUDENT (count student' skills + level.id) // UNUSED //
        $completeLevels = [];
        foreach($countSkillsLevel as $id => $skillsNbr) {
            foreach($countStudentSkillsLevel as $skillsStudentNbr) {
                if($skillsNbr['id'] == $skillsStudentNbr['id']) {
                    $completeLevels[] = $skillsNbr;
                }
            }
        }

        // ARRAY OF COMPLETE LEVEL'S ID     // UNUSED //
        /*$levelId = [];
        foreach ($completeLevels as $completeLevel) {
            array_push($levelId, $completeLevel['id']); //$levelId[] = $completeLevel['id']; = the same result
        }*/


        // ALL STUDENT'S SKILLS FOR A DISCIPLINE
        $studentSkills = $this->createQueryBuilder('s')
                    ->leftJoin('s.studentSkills', 'ss')
                    ->join('s.disciplineLevel', 'l')
                    ->join('l.discipline', 'd')
                    ->where('ss.student= :student')
                    ->andWhere('d.name = :name')
                    ->setParameter('student', $student)
                    ->setParameter('name', $discipline)
                    ->getQuery()
                    ->getResult();

        // ALL SKILLS FOR A DISCIPLINE
        $skills = $this->createQueryBuilder('s')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->andWhere('d.name = :name')
            ->setParameter('name', $discipline)
            ->getQuery()
            ->getResult();


        // DIFF BETWEEN 2 ARRAYS = ALL SKILLS NOT ASSIGNED (FOR A STUDENT)
        $nullSkills = array_diff($skills, $studentSkills);

        // SIMPLE ARRAY WITH SKILLS NOT ASSIGNED WITH LEVELS.ID (key => level.id) [0 => 4, 1 => 4, 2 =>5]
        $arrayLevels = [];
        foreach ($nullSkills as $skill) {
            $level = $skill->getDisciplineLevel()->getId();
            array_push($arrayLevels, $level);
        }

        // COUNT SKILLS NOT ASSIGNED GROUP BY LEVELS.ID (level.id => countSkills) [4 => 2, 5 => 1]
        $countArrayLevels = array_count_values($arrayLevels);

        // CURRENT LEVEL = FIRST LEVEL TO COMPLETE = 4
        $currentLevel = key($countArrayLevels);


        // QB = ALL SKILLS NOT ASSIGNED (FOR A STUDENT)
        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->join('s.disciplineLevel', 'l')
            ->andWhere('s IN (:nullSkills)')
            ->andwhere('l.id = :currentLevel')
            ->setParameter('nullSkills', $nullSkills)
            ->setParameter('currentLevel', $currentLevel);

        return $qb;

    }


    // get skills for a student's current level
    public function findByStudent($student, $discipline)
    {
        // QB = ALL STUDENT'S SKILLS FOR A DISCIPLINE
        $studentSkills = $this->createQueryBuilder('s')
            ->leftJoin('s.studentSkills', 'ss')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->where('ss.student= :student')
            ->andWhere('d.name = :name')
            ->setParameter('student', $student)
            ->setParameter('name', $discipline)
            ->getQuery()
            ->getResult();

        // QB = ALL SKILLS FOR A DISCIPLINE
        $skills = $this->createQueryBuilder('s')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->andWhere('d.name = :name')
            ->setParameter('name', $discipline)
            ->getQuery()
            ->getResult();

        // DIFF BETWEEN 2 ARRAYS = ALL SKILLS NOT ASSIGNED (FOR A STUDENT)
        $nullSkills = array_diff($skills, $studentSkills);

        // SIMPLE ARRAY WITH SKILLS NOT ASSIGNED WITH LEVELS.ID (key => level.id) [0 => 4, 1 => 4, 2 =>5]
        $arrayLevels = [];
        foreach ($nullSkills as $skill) {
            $level = $skill->getDisciplineLevel()->getId();
            array_push($arrayLevels, $level);
        }

        // COUNT SKILLS NOT ASSIGNED GROUP BY LEVELS.ID (level.id => countSkills) [4 => 2, 5 => 1]
        $countArrayLevels = array_count_values($arrayLevels);

        // CURRENT LEVEL = FIRST LEVEL TO COMPLETE = 4
        $currentLevel = key($countArrayLevels);

        return $this->createQueryBuilder('s')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->join('s.studentSkills', 'ss')
            ->join('ss.student', 'st')
            ->where('st.id = :student')
            ->andWhere('d.name = :discipline')
            ->andWhere('l.id = :currentLevel')
            ->addSelect('l')
            ->setParameter('student', $student)
            ->setParameter('discipline', $discipline)
            ->setParameter('currentLevel', $currentLevel)
            ->getQuery()
            ->getResult();
    }

    public function countSkillsForDisciplineAndStudent($discipline, $student, $career)
    {
        // ALL SKILLS FOR A DISCIPLINE
        $skills = $this->createQueryBuilder('s')
            ->select('COUNT(s) as totalSkills')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->join('l.academicCareers', 'c')
            ->andWhere('d.id = :discipline')
            ->andWhere('c.id = :career')
            ->setParameter('discipline', $discipline)
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();

        // ALL STUDENT'S SKILLS FOR A DISCIPLINE
        $studentSkills = $this->createQueryBuilder('s')
            ->select('COUNT(s) as totalStudentSkills')
            ->join('s.studentSkills', 'ss')
            ->join('s.disciplineLevel', 'l')
            ->join('l.discipline', 'd')
            ->join('l.academicCareers', 'c')
            ->where('ss.student= :student')
            ->andWhere('d.id = :discipline')
            ->andWhere('c.id = :career')
            ->setParameter('student', $student)
            ->setParameter('discipline', $discipline)
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();

        $array = $skills + $studentSkills;

        return $array;
    }

    public function countSkillsByPathwayAndGrade($pathway, $grade)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s) as skills')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->andWhere('c.pathway = :pathway')
            ->andWhere('c.grade = :grade')
            ->setParameter('pathway', $pathway)
            ->setParameter('grade', $grade)
            ->getQuery()
            ->getSingleResult();
    }

    public function countSkillsByCareer($career)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s) as skills')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->andWhere('c.id = :career')
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByProfession($profession)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s) as skills')
            ->join('s.disciplineLevel', 'l')
            ->join('l.academicCareers', 'c')
            ->join('c.profession', 'p')
            ->andWhere('p.id = :pro')
            ->setParameter('pro', $profession)
            ->getQuery()
            ->getSingleResult();
    }
}
