<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Discipline;
use App\Entity\StudentSkill;
use App\Form\StudentEvaluationType;
use App\Form\StudentSkillType;
use App\Repository\DisciplineLevelRepository;
use App\Repository\DisciplineRepository;
use App\Repository\SectionRepository;
use App\Repository\SkillRepository;
use App\Repository\StudentRepository;
use App\Repository\StudentSkillRepository;
use App\Repository\TeacherRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends AbstractController
{

    /**
     * Get an array with all discipline's names
     *
     * @param DisciplineRepository $disciplineR
     * @param TeacherRepository $teacherR
     * @return array
     */
    private function getDisciplineNames(DisciplineRepository $disciplineR, TeacherRepository $teacherR)
    {
        if ($this->isGranted('ROLE_TEACHER')) {
            $disciplineNames = $disciplineR->findNamesByTeacher($this->getUser()->getTeacher(), $teacherR);
        } else {
            $disciplineNames = $disciplineR->findAllNames();
        }

        $disciplineNamesA = [];
        foreach ($disciplineNames as $disciplineName) {
            array_push($disciplineNamesA, $disciplineName['name']);
        }
        return $disciplineNamesA;
    }


    /**
     * @param Request $request
     * @param SectionRepository $sectionR
     * @param StudentRepository $studentR
     * @param DisciplineRepository $disciplineR
     * @param TeacherRepository $teacherR
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/evaluation", name="evaluation")
     */
    public function selectStudentToEvaluate(Request $request, SectionRepository $sectionR, StudentRepository $studentR, DisciplineRepository $disciplineR, TeacherRepository $teacherR)
    {
        $page = 'evaluation';

        $sectionName = $request->request->get('section');
        $discipline = $request->request->get('discipline');

        $sections = $sectionR->findAllSectionsOrderByName();
        $students = $studentR->findAll();

        if ($this->isGranted('ROLE_TEACHER')) {
            $disciplines = $disciplineR->findByTeacher($this->getUser()->getTeacher(), $teacherR);
        } else {
            $disciplines = $disciplineR->findAll();
        }
        $disciplineNamesA = $this->getDisciplineNames($disciplineR, $teacherR);

        /*$disciplineNames = $disciplineR->findAllNames();
        $disciplineNamesA = [];
        foreach ($disciplineNames as $disciplineName) {
            array_push($disciplineNamesA, $disciplineName['name']);
        }*/

        $skill = new StudentSkill();
        $form = $this->createForm(StudentSkillType::class, $skill);
        $form->handleRequest($request);


        return $this->render('evaluation/page_evaluation.html.twig', [
            'page' => $page,
            'skill' => $skill,
            'students' => $students,
            'sections' => $sections,
            'disciplines' => $disciplines,
            'sectionName' => $sectionName,
            'discipline' => $discipline,
            'disciplineNames' => $disciplineNamesA
        ]);
    }

    /**
     *
     * Find a student to evaluate
     *
     * @param Request $request
     * @param SectionRepository $sectionR
     * @param StudentRepository $studentR
     * @param DisciplineRepository $disciplineR
     * @param TeacherRepository $teacherR
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/evaluation/section/", name="evaluation_find_student")
     */
    public function findStudentToEvaluate(SectionRepository $sectionR, StudentRepository $studentR, DisciplineRepository $disciplineR, Request $request, TeacherRepository $teacherR)
    {
        $page = 'evaluation_student';

        $section = $request->query->get('section');
        $discipline = $request->query->get('discipline');
        $disciplineId = $disciplineR->findByName($discipline)['id'];

        $sections = $sectionR->findAll();
        $disciplines = $disciplineR->findAll();
        $disciplineNamesA = $this->getDisciplineNames($disciplineR, $teacherR);
        $students = $studentR->findStudentBySection($section);

        return $this->render('evaluation/evaluation_find_student.html.twig', [
            'page' => $page,
            'students' => $students,
            'sections' => $sections,
            'disciplines' => $disciplines,
            'section' => $section,
            'discipline' => $discipline,
            'disciplineId' => $disciplineId,
            'disciplineNames' => $disciplineNamesA
        ]);
    }


    /**
     * Evaluate a student with selected values in form (2 lasts methods)
     *
     * @param Request $request
     * @param ObjectManager $m
     * @param SectionRepository $sectionR
     * @param StudentRepository $studentR
     * @param DisciplineRepository $disciplineR
     * @param TeacherRepository $teacherR
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_TEACHER') and user.getTeacher().getDiscipline().contains(disciplineId))")
     *
     * @Route("/evaluation/etudiant/{disciplineId}/", name="evaluation_form")
     */
    public function evaluationAction(Discipline $disciplineId, Request $request, ObjectManager $m, SectionRepository $sectionR, StudentRepository $studentR,
                                     DisciplineRepository $disciplineR, SkillRepository $skillR, DisciplineLevelRepository $disciplineLevelR, TeacherRepository $teacherR)
    {
        $page = 'evaluation_form';

        // get back GET params because of forms (2 lasts methods)
        $section = $request->query->get('section');
        $discipline = $request->query->get('discipline');
        $studentId = $request->query->get('student');

        $student = $studentR->findOneBy(['id'=>$studentId]);

        $sections = $sectionR->findAll();
        $disciplines = $disciplineR->findAll();
        $disciplineNamesA = $this->getDisciplineNames($disciplineR, $teacherR);
        // get back all students in selected section (in last form)
        $students = $studentR->findStudentBySection($section);

        // get back all discipline's levels for the selected discipline (in last form)
        $disciplineLevels = $disciplineLevelR->findDisciplineLevels($discipline);

        // get back all skills for this student's current level
        $studentSkills = $skillR->findByStudent($student, $discipline);
        // get back all skills to earn for this student
        $studentSkillsToEarn = $skillR->findByDiscipline($discipline, $student)->getParameters()->getValues()[0]->getValue();

        /*if (empty($studentSkillsToEarn)) {
            $this->addFlash(
                'fail',
                "Toutes les compétences ont été attribuées pour l'étudiant " . $student . " et la discipline "  . $discipline . "."
            );
        }*/

        // get back the user is granted
        $user = $this->getUser();

        $skill = new StudentSkill();
        $comment = new Comment();

        // if the connected user is a teacher, get back his id
        if ($this->isGranted('ROLE_TEACHER')) {
            $teacher = $user->getTeacher();
            $comment->setTeacher($teacher);
        } else {
            $team = $user->getTeam();
            $comment->setTeam($team);
        }

        $comment->setStudent($student)
            ->setDiscipline($disciplineR->findOneBy(['name' => $discipline]));
        $skill->addComment($comment);

        $form = $this->createForm(StudentEvaluationType::class, $skill, ['discipline' => $discipline, 'student' => $student]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($skill->getComment() as $comment) {
                if ($skill->getComment()->getValues()[0]->getContent() === null) {
                    $comment->getStudentSkill()->removeComment($comment);
                } else {
                    $comment->setStudentSkill($skill);
                    $m->persist($comment);
                }
            }

            if ($this->isGranted('ROLE_TEACHER')) {
                $skill->setTeacher($teacher);
            } else {
                $skill->setTeam($team);
            }

            $skill->setStudent($student);
            $m->persist($skill);
            $m->flush();

            $this->addFlash(
                'succes',
                "La compétence a bien été enregistrée."
            );

            return $this->redirect($request->getUri());
        }

        if ($this->isGranted('ROLE_ADMIN') or ($this->isGranted('ROLE_TEACHER') and in_array($disciplineR->findByName($discipline)['id'], $teacherR->findDisciplinesByTeacher($this->getUser()->getTeacher())))) {
            return $this->render('evaluation/evaluation_form.html.twig', [
                'page' => $page,
                'students' => $students,
                'sections' => $sections,
                'disciplines' => $disciplines,
                'disciplineNames' => $disciplineNamesA,
                'student' => $student,
                'section' => $section,
                'discipline' => $discipline,
                'disciplineId' => $disciplineId,
                'skills' => $studentSkills,
                'studentSkills' => $studentSkills,
                'studentSkillsToEarn' => $studentSkillsToEarn,
                'disciplineLevels' => $disciplineLevels,
                'skillsForm' => $form->createView()
            ]);
        } else {
            return $this->redirectToRoute('evaluation');
        }
    }

}
