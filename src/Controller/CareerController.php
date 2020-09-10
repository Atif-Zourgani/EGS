<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Repository\AcademicCareerRepository;
use App\Repository\DisciplineRepository;
use App\Repository\GradeRepository;
use App\Repository\PathwayRepository;
use App\Repository\PathwaySpecialismRepository;
use App\Repository\ProfessionRepository;
use App\Repository\SkillRepository;
use App\Repository\TeacherRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CareerController extends AbstractController
{
    /**
     * Return academic careers' homepage
     *
     * @param PathwayRepository $pathwayR
     * @param GradeRepository $gradeR
     * @param ProfessionRepository $professionR
     * @param AcademicCareerRepository $careerR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/parcours", name="all_careers")
     */
    public function allCareers(PathwayRepository $pathwayR, GradeRepository $gradeR, ProfessionRepository
    $professionR, AcademicCareerRepository $careerR)
    {
        $page = 'careers';

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
                    $career = $careerR->findCareerBySpecialism($pathway['name'], $grade->getName(), 1);
                } else {
                    $career = $careerR->findCareerBy($pathway['name'], $grade->getName());
                }

                $careerE['career'] = $career;

                if ($career == '') {
                    $array = $gradeId + $gradeName;
                } else {
                    $array = $careerE + $gradeId + $gradeName;
                }
                //$array = $careerE + $gradeId + $gradeName;
                array_push($gradesA, $array);
            }

            $pathwayAndGrades = $pathway + ['grades' => $gradesA];
            array_push($pathwaysA, $pathwayAndGrades);
        }

        return $this->render('careers/page_all_careers.html.twig', [
            'page' => $page,
            'grades' => $grades,
            'professions' => $professions,
            'pathways' => $pathwaysA,
        ]);

    }


    /**
     * Return all disciplines and discipline's levels for an academic's career
     *
     * @param $career
     * @param AcademicCareerRepository $academicCareerR
     * @param DisciplineRepository $disciplineR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/parcours/{career}/", name="career")
     */
    public function career($career, AcademicCareerRepository $academicCareerR,
                           DisciplineRepository $disciplineR, TeacherRepository $teacherR)
    {
        $page = 'career';

        $career = $academicCareerR->findCareerById($career);

        $disciplines = $disciplineR->findDisciplinesByCareer($career);

        if($this->isGranted('ROLE_TEACHER')) {
            return $this->render('careers/page_career.html.twig', [
                'page' => $page,
                'disciplines' => $disciplines,
                'career' => $career,
                'teacherDisciplines' => $teacherR->findDisciplinesByTeacher($this->getUser()->getTeacher()->getId()),
            ]);
        } else {
            return $this->render('careers/page_career.html.twig', [
                'page' => $page,
                'disciplines' => $disciplines,
                'career' => $career
                ]);
        }

    }

    /**
     * Return all disciplines and discipline's levels for an academic's career
     *
     * @param $specialism
     * @param $career
     * @param AcademicCareerRepository $academicCareerR
     * @param DisciplineRepository $disciplineR
     * @param PathwaySpecialismRepository $specialismR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/parcours/{career}/specialite/{specialism}", name="specialism_career")
     */
    public function specialismCareer($career, $specialism, AcademicCareerRepository $academicCareerR,
                           DisciplineRepository $disciplineR, PathwaySpecialismRepository $specialismR)
    {
        $page = 'career';

        $careerEntity = $academicCareerR->findOneBy(['id' => $career]);
        $specialisms = $specialismR->findAllWithCareer($career);
        $specialismEntity = $specialismR->findOneBy(['id' => $specialism]);

        $disciplines = $disciplineR->findDisciplinesByCareer($career);

        return $this->render('careers/page_career.html.twig', [
            'page' => $page,
            'disciplines' => $disciplines,
            'career' => $careerEntity,
            'specialisms' => $specialisms,
            'specialism' => $specialismEntity
        ]);
    }


    /**
     * Return all disciplines and discipline's levels for an academic's career for a profession
     *
     * @param $profession
     * @param $career
     * @param AcademicCareerRepository $academicCareerR
     * @param DisciplineRepository $disciplineR
     * @param ProfessionRepository $professionR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/parcours-pro/{career}/{profession}", name="pro_career")
     */
    public function proCareer($profession, $career, AcademicCareerRepository $academicCareerR,
                           DisciplineRepository $disciplineR, ProfessionRepository $professionR)
    {
        $page = 'career';

        $proName = str_replace("-", " ", $profession);
        $professionEntity = $professionR->findOneBy(['name' => $proName]);

        $careerEntity = $academicCareerR->findOneBy(['id' => $career]);

        $disciplines = $disciplineR->findDisciplineByProfession($proName);

        return $this->render('careers/page_career.html.twig', [
            'page' => $page,
            'profession' => $professionEntity,
            'disciplines' => $disciplines,
            'career' => $careerEntity
        ]);
    }


    /**
     * Return a discipline with skills for all discipline's level's career
     *
     * @param $discipline
     * @param $career
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param AcademicCareerRepository $academicCareerR
     * @param PathwayRepository $pathwayR
     * @param GradeRepository $gradeR
     * @param ProfessionRepository $professionR
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_TEACHER') and user.getTeacher().getDiscipline().contains(discipline))")
     *
     * @Route("/parcours-pro/{career}/discipline/{discipline}", name="discipline_pro_career")
     *
     * @Route("/parcours/{career}/discipline/{discipline}", name="discipline_career")
     */
    public function disciplineCareer(Discipline $discipline, $career, DisciplineRepository $disciplineR, SkillRepository
    $skillR, AcademicCareerRepository $academicCareerR, PathwayRepository $pathwayR, GradeRepository $gradeR, ProfessionRepository $professionR, Request $request)
    {
        $page = 'discipline_career';

        $careerEntity = $academicCareerR->findOneBy(['id' => $career]);

        $disciplineName = $disciplineR->findDisciplineAndSkillsByCareer($careerEntity, $discipline);

        $skills = $skillR->sumAllSkillsByCareer($discipline, $career);

        if ($request->attributes->get('_route') == 'discipline_career') {

            $pathway = $pathwayR->findByCareer($career);

            $grade = $gradeR->findByCareer($career);

            return $this->render('page_discipline.html.twig', [
                'page' => $page,
                'discipline' => $disciplineName,
                'skills' => $skills['total'],
                'pathway' => $pathway,
                'grade' => $grade,
                'career' => $career
            ]);

        } else {

            $profession = $professionR->findByCareer($career);

            return $this->render('page_discipline.html.twig', [
                'page' => $page,
                'discipline' => $disciplineName,
                'skills' => $skills['total'],
                'career' => $career,
                'profession' => $profession,
            ]);
        }
    }

    /**
     * Return a discipline with skills for all discipline's level's career and student's skills infos
     *
     * @param $discipline
     * @param $career
     * @param $specialism
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @param PathwayRepository $pathwayR
     * @param PathwaySpecialismRepository $specialismR
     * @param GradeRepository $gradeR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/parcours/{career}/{specialism}/discipline/{discipline}", name="discipline_specialism_career")
     */
    public function studentDisciplineCareerWithSpecialism($discipline, $career, $specialism, DisciplineRepository $disciplineR, SkillRepository $skillR,
                                                          PathwayRepository $pathwayR, GradeRepository $gradeR, PathwaySpecialismRepository $specialismR)
    {
        $page = 'discipline_career';

        $disciplineEntity = $disciplineR->findDisciplineAndSkillsByCareer($career, $discipline);

        $pathway = $pathwayR->findByCareer($career);

        $grade = $gradeR->findByCareer($career);
        $spe = $specialismR->findOneby(['id' => $specialism]);

        $skills = $skillR->sumAllSkillsByCareer($discipline, $career);

        return $this->render('page_discipline.html.twig', [
            'page' => $page,
            'discipline' => $disciplineEntity,
            'pathway' => $pathway,
            'grade' => $grade,
            'specialism' => $spe,
            'career' => $career,
            'skills' => $skills['total']
        ]);

    }
}
