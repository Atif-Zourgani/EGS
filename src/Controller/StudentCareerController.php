<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\AcademicCareerRepository;
use App\Repository\DisciplineRepository;
use App\Repository\GradeRepository;
use App\Repository\PathwayRepository;
use App\Repository\PathwaySpecialismRepository;
use App\Repository\ProfessionRepository;
use App\Repository\SkillRepository;
use App\Repository\StudentRepository;
use App\Repository\StudentSkillRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class StudentCareerController extends AbstractController
{

    /**
     * Return academic careers' homepage for a student
     *
     * @param $student
     * @param PathwayRepository $pathwayR
     * @param GradeRepository $gradeR
     * @param ProfessionRepository $professionR
     * @param StudentSkillRepository $studentSkillR
     * @param SkillRepository $skillR
     * @param StudentRepository $studentR
     * @param AcademicCareerRepository $careerR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/{student}/parcours", name="student_careers")
     */
    public function allStudentCareers(Student $student, PathwayRepository $pathwayR, GradeRepository $gradeR, ProfessionRepository
    $professionR, StudentSkillRepository $studentSkillR, SkillRepository $skillR, StudentRepository $studentR, AcademicCareerRepository $careerR)
    {
        $page = 'careers';

        $student = $studentR->findOneBy(['id'=>$student]);
        $pathways = $pathwayR->findAllPathways();
        $grades = $gradeR->findAll();
        $professions = $professionR->findAllWithCareer();

        $pathwaysA = [];
        foreach ($pathways as $pathway) {

            $gradesA = [];
            foreach ($grades as $grade) {

                $gradeName['name'] = $grade->getName();
                $gradeShort['shortname'] = $grade->getShortname();
                $gradeId['id'] = $grade->getId();

                if ($grade->getId() == 4 or $grade->getId() == 5) {
                    $career = $careerR->findCareerBySpecialism($pathway['name'], $grade->getName(), $specialism = 1);
                } else {
                    $career = $careerR->findCareerBy($pathway['name'], $grade->getName());
                }

                if ($career != null) {

                    // percentage of students' skills
                    $studentSkills = $studentSkillR->countSkillsByStudentAndCareer($student, $career);

                    $skills = $skillR->countSkillsByCareer($career->getId());

                    if ($studentSkills['studentSkills'] > 0 and $skills['skills'] > 0) {
                        $percentage['percentage'] = ($studentSkills['studentSkills'] / $skills['skills']) * 100;
                    } else {
                        $percentage['percentage'] = 0;
                    }

                    $careerE['career'] = $career;

                    $array = $skills + $studentSkills + $careerE + $gradeId + $gradeName + $gradeShort + $percentage;
                    array_push($gradesA, $array);

                } else {
                    $array =  $gradeId + $gradeName + $gradeShort;
                    array_push($gradesA, $array);
                }

            }

            $pathwayAndGrades = $pathway + ['grades' => $gradesA];
            array_push($pathwaysA, $pathwayAndGrades);
        }


        // add percentage of students' skills for a each profession
        $professionsA = [];

        foreach ($professions as $profession) {

            $studentSkills = $studentSkillR->countSkillsByStudentAndProfession($student, $profession['id']);
            $skills = $skillR->findByProfession($profession['id']);

            if ($studentSkills['studentSkills'] > 0 and $skills['skills'] > 0) {
                $percentage = ($studentSkills['studentSkills'] / $skills['skills']) * 100;
            } else {
                $percentage = 0;
            }

            $job = $profession + ['percentage' => $percentage];

            array_push($professionsA, $job);
        }


        return $this->render('careers/page_all_careers.html.twig', [
            'page' => $page,
            'pathways' => $pathwaysA,
            'grades' => $grades,
            'professions' => $professionsA,
            'student' => $student
        ]);

    }


    /**
     * Return all disciplines and discipline's levels for an academic's career and a student
     *
     * @param $student
     * @param $career
     * @param AcademicCareerRepository $academicCareerR
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param StudentSkillRepository $studentSkillR
     * @param StudentRepository $studentR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/{student}/parcours/{career}/", name="student_career")
     */
    public function studentCareer(Student $student, $career, AcademicCareerRepository $academicCareerR,
                           DisciplineRepository $disciplineR, SkillRepository $skillR, StudentSkillRepository
                           $studentSkillR, StudentRepository $studentR)
    {
        $page = 'career';

        $career = $academicCareerR->findCareerById($career);
        $disciplines = $disciplineR->findDisciplinesByCareer($career);
        $student = $studentR->findOneBy(['id'=>$student]);

        $disciplinesA = [];
        foreach($disciplines as $discipline)
        {
            $disciplineLevels = [];
            foreach ($discipline['disciplineLevels'] as $disciplineLevel) {
                $skillsTotal = $skillR->findBy(['disciplineLevel' => $disciplineLevel['id']]);
                $level['levelId'] = $disciplineLevel['id'];
                $skills['levelSkills'] = count($skillsTotal);
                $studentSkills = $studentSkillR->findByStudentAndLevel($student, $level);

                $disciplineInfos = $level + $skills + $studentSkills;

                array_push($disciplineLevels, $disciplineInfos);
            }

            $skillsA = $skillR->countSkillsForDisciplineAndStudent($discipline['id'], $student, $career);
            $percentage['percentage'] = ($skillsA['totalStudentSkills'] / $skillsA['totalSkills']) * 100;
            $levels['levelsTotal'] = count($discipline['disciplineLevels']);

            $array = $discipline + $skillsA + $percentage + $levels + ['levels' => $disciplineLevels];

            array_push($disciplinesA, $array);
        }

        $studentSkills = $studentSkillR->countSkillsByStudentAndCareer($student, $career);
        $skills = $skillR->countSkillsByCareer($career);

        if ($studentSkills['studentSkills'] > 0 and $skills['skills'] > 0) {
            $percentage['percentage'] = ($studentSkills['studentSkills'] / $skills['skills']) * 100;
        } else {
            $percentage['percentage'] = 0;
        }

        return $this->render('careers/page_career.html.twig', [
            'page' => $page,
            'disciplines' => $disciplinesA,
            'career' => $career,
            'student' => $student,
            'percentage' => $percentage['percentage']
        ]);
    }

    /**
     * Return all disciplines and discipline's levels for an academic's career and a student
     *
     * @param $student
     * @param $career
     * @param $specialism
     * @param AcademicCareerRepository $academicCareerR
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param StudentSkillRepository $studentSkillR
     * @param StudentRepository $studentR
     * @param PathwaySpecialismRepository $specialismR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/{student}/parcours/{career}/specialite/{specialism}", name="student_specialism_career")
     */
    public function studentSpecialityCareer(Student $student, $career, $specialism, AcademicCareerRepository $academicCareerR,
                                  DisciplineRepository $disciplineR, SkillRepository $skillR, StudentSkillRepository
                                  $studentSkillR, StudentRepository $studentR, PathwaySpecialismRepository $specialismR)
    {
        $page = 'career';

        $student = $studentR->findOneBy(['id'=>$student]);

        $careerEntity = $academicCareerR->findOneBy(['id' => $career]);

        $specialisms = $specialismR->findAllWithCareer($career);
        $specialismEntity = $specialismR->findOneBy(['id' => $specialism]);

        $disciplines = $disciplineR->findDisciplinesByCareer($career);

        $studentSkills = $studentSkillR->countSkillsByStudentAndCareer($student, $career);
        $skills = $skillR->countSkillsByCareer($careerEntity);

        if ($studentSkills['studentSkills'] > 0 and $skills['skills'] > 0) {
            $totalPercentage['percentage'] = ($studentSkills['studentSkills'] / $skills['skills']) * 100;
        } else {
            $totalPercentage['percentage'] = 0;
        }

        $disciplinesA = [];
        foreach($disciplines as $discipline)
        {
            $disciplineLevels = [];
            foreach ($discipline['disciplineLevels'] as $disciplineLevel) {
                $skillsTotal = $skillR->findBy(['disciplineLevel' => $disciplineLevel['id']]);
                $level['levelId'] = $disciplineLevel['id'];
                $skills['levelSkills'] = count($skillsTotal);
                $studentSkills = $studentSkillR->findByStudentAndLevel($student, $level);

                $disciplineInfos = $level + $skills + $studentSkills;

                array_push($disciplineLevels, $disciplineInfos);
            }

            $skillsA = $skillR->countSkillsForDisciplineAndStudent($discipline['id'], $student, $career);

            $percentage['percentage'] = ($skillsA['totalStudentSkills'] / $skillsA['totalSkills']) * 100;

            $levels['levelsTotal'] = count($discipline['disciplineLevels']);
            $array = $discipline + $skillsA /*+ $percentage*/ + $levels + ['levels' => $disciplineLevels];

            array_push($disciplinesA, $array);
        }


        return $this->render('careers/page_career.html.twig', [
            'page' => $page,
            'specialisms' => $specialisms,
            'specialism' => $specialismEntity,
            'disciplines' => $disciplinesA,
            'career' => $careerEntity,
            'student' => $student,
            'percentage' => $totalPercentage['percentage']
        ]);
    }

    /**
     * Return all disciplines and discipline's levels for an academic's career for a profession and a student
     *
     * @param $student
     * @param $profession
     * @param $career
     * @param AcademicCareerRepository $academicCareerR
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param StudentSkillRepository $studentSkillR
     * @param StudentRepository $studentR
     * @param ProfessionRepository $professionR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/{student}/parcours-pro/{career}/{profession}", name="student_pro_career")
     */
    public function studentProCareer($profession, Student $student, $career, AcademicCareerRepository $academicCareerR, DisciplineRepository $disciplineR,
                                     StudentRepository $studentR, SkillRepository $skillR, StudentSkillRepository $studentSkillR, ProfessionRepository $professionR)
    {
        $page = 'career';

        $student = $studentR->findOneBy(['id' => $student]);

        $proName = str_replace("-", " ", $profession);
        $professionEntity = $professionR->findOneBy(['name' => $proName]);

        $careerEntity = $academicCareerR->findOneBy(['id' => $career]);

        $disciplines = $disciplineR->findDisciplineByProfessionForStudent($proName);

        $studentSkills = $studentSkillR->countSkillsByStudentAndCareer($student, $career);
        $skills = $skillR->countSkillsByCareer($careerEntity);

        if ($studentSkills['studentSkills'] > 0 and $skills['skills'] > 0) {
            $totalPercentage['percentage'] = ($studentSkills['studentSkills'] / $skills['skills']) * 100;
        } else {
            $totalPercentage['percentage'] = 0;
        }

        $disciplinesA = [];
        foreach($disciplines as $discipline)
        {
            $disciplineLevels = [];
            foreach ($discipline['disciplineLevels'] as $disciplineLevel) {
                $skillsTotal = $skillR->findBy(['disciplineLevel' => $disciplineLevel['id']]);
                $level['levelId'] = $disciplineLevel['id'];
                $skills['levelSkills'] = count($skillsTotal);
                $studentSkills = $studentSkillR->findByStudentAndLevel($student, $level);

                $disciplineInfos = $level + $skills + $studentSkills;

                array_push($disciplineLevels, $disciplineInfos);
            }

            $skillsA = $skillR->countSkillsForDisciplineAndStudent($discipline['id'], $student, $career);
            $percentage['percentage'] = ($skillsA['totalStudentSkills'] / $skillsA['totalSkills']) * 100;
            $levels['levelsTotal'] = count($discipline['disciplineLevels']);

            $array = $discipline + $skillsA + $percentage + $levels + ['levels' => $disciplineLevels];

            array_push($disciplinesA, $array);
        }

        return $this->render('careers/page_career.html.twig', [
            'page' => $page,
            'disciplines' => $disciplinesA,
            'career' => $careerEntity,
            'student' => $student,
            'profession' => $professionEntity,
            'percentage' => $totalPercentage['percentage']
        ]);
    }


    /**
     * Return a discipline with skills for all discipline's level's career and student's skills infos
     *
     * @param $student
     * @param $discipline
     * @param $career
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param AcademicCareerRepository $academicCareerR
     * @param StudentRepository $studentR
     * @param PathwayRepository $pathwayR
     * @param GradeRepository $gradeR
     * @param ProfessionRepository $professionR
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/{student}/parcours-pro/{career}/discipline/{discipline}", name="student_discipline_pro_career")
     *
     * @Route("/{student}/parcours/{career}/discipline/{discipline}", name="student_discipline_career")
     */
    public function studentDisciplineCareer(Student $student, $discipline, $career, DisciplineRepository $disciplineR, SkillRepository $skillR,
                                            AcademicCareerRepository $academicCareerR, StudentRepository $studentR, PathwayRepository $pathwayR, GradeRepository $gradeR,
                                            ProfessionRepository $professionR, Request $request)
    {
        $page = 'discipline_career';

        $student = $studentR->findOneBy(['id'=>$student]);

        $careerEntity = $academicCareerR->findOneBy(['id' => $career]);

        $disciplineEntity = $disciplineR->findDisciplineAndSkillsByCareer($careerEntity, $discipline);

        $skills = $skillR->countSkillsForDisciplineAndStudent($disciplineEntity, $student, $careerEntity);
        $studentPercentage = ($skills['totalStudentSkills'] / $skills['totalSkills']) * 100;

        if ($request->attributes->get('_route') == 'student_discipline_career') {

            $pathway = $pathwayR->findByCareer($career);
            $grade = $gradeR->findByCareer($career);

            return $this->render('page_discipline.html.twig', [
                'page' => $page,
                'discipline' => $disciplineEntity,
                'skills' => $skills['totalSkills'],
                'student' => $student,
                'studentPercentage' => $studentPercentage,
                'pathway' => $pathway,
                'career' => $career,
                'grade' => $grade
            ]);

        } else {

            $profession = $professionR->findByCareer($career);

            return $this->render('page_discipline.html.twig', [
                'page' => $page,
                'discipline' => $disciplineEntity,
                'skills' => $skills['totalSkills'],
                'student' => $student,
                'studentPercentage' => $studentPercentage,
                'career' => $career,
                'profession' => $profession,
            ]);

        }

    }

    /**
     * Return a discipline with skills for all discipline's level's career and student's skills infos
     *
     * @param $student
     * @param $discipline
     * @param $career
     * @param $specialism
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param StudentRepository $studentR
     * @param PathwaySpecialismRepository $specialismR
     * @param PathwayRepository $pathwayR
     * @param GradeRepository $gradeR
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/{student}/parcours/{career}/{specialism}/discipline/{discipline}", name="student_discipline_specialism_career")
     */
    public function studentDisciplineCareerWithSpecialism(Student $student, $discipline, $career, $specialism, DisciplineRepository $disciplineR, SkillRepository
    $skillR, StudentRepository $studentR, PathwayRepository $pathwayR, GradeRepository $gradeR, Request $request, PathwaySpecialismRepository $specialismR)
    {
        $page = 'discipline_career';

        $student = $studentR->findOneBy(['id'=>$student]);

        $disciplineEntity = $disciplineR->findDisciplineAndSkillsByCareer($career, $discipline);

        $skills = $skillR->countSkillsForDisciplineAndStudent($disciplineEntity, $student, $career);
        $studentPercentage = ($skills['totalStudentSkills'] / $skills['totalSkills']) * 100;

        $pathway = $pathwayR->findByCareer($career);

       if ($request->attributes->get('_route') == 'student_discipline_specialism_career') {

            $grade = $gradeR->findByCareer($career);
            $spe = $specialismR->findOneby(['id' => $specialism]);

            return $this->render('page_discipline.html.twig', [
                'page' => $page,
                'discipline' => $disciplineEntity,
                'skills' => $skills['totalSkills'],
                'student' => $student,
                'studentPercentage' => $studentPercentage,
                'pathway' => $pathway,
                'grade' => $grade,
                'specialism' => $spe,
                'career' => $career
            ]);

        }
    }



}
