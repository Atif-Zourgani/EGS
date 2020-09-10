<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Entity\Student;
use App\Form\SearchDisciplineType;
use App\Repository\DisciplineCatRepository;
use App\Repository\DisciplineRepository;
use App\Repository\SkillRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisciplineController extends AbstractController
{
    /**
     * @param DisciplineCatRepository $disciplineCatR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/disciplines", name="all_disciplines")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function allDisciplines(DisciplineCatRepository $disciplineCatR, TeacherRepository $teacherR)
    {
        $page = 'all_disciplines';
        $discipline = new Discipline();
        $form = $this->createForm(SearchDisciplineType::class, $discipline);

        //$result = $disciplineR->findAllDisciplinesByCategory();
        $result = $disciplineCatR->findAllCategories();

        if ($this->isGranted('ROLE_TEACHER')) {
            return $this->render('page_all_disciplines.html.twig', [
                'page' => $page,
                'categories' => $result,
                'teacherDisciplines' => $teacherR->findDisciplinesByTeacher($this->getUser()->getTeacher()->getId()),
                "formSearch" => $form->createView()
            ]);
        } else {
            return $this->render('page_all_disciplines.html.twig', [
                'page' => $page,
                'categories' => $result,
                "formSearch" => $form->createView()
            ]);
        }

    }

    /**
     * @param DisciplineRepository $disciplineR
     * @param Request $request
     * @return JsonResponse
     * @Route("/disciplines/recherche", name="discipline_search")
     */
    public function disciplineSearchAction(DisciplineRepository $disciplineR, Request $request)
    {
        $query = $request->get('searchText');
        $disciplines = $disciplineR->findByQuery($query);

        if(!$disciplines) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealDisciplines($disciplines);
        }

        return new JsonResponse($result); // translate to json
    }

    public function getRealDisciplines($disciplines)
    {
        foreach ($disciplines as $discipline){
            $realDisciplines[$discipline->getId()] = [$discipline->getName()];
        }
        return $realDisciplines;
    }

    /**
     * @param $discipline
     * @param DisciplineRepository $disciplineR
     * @param SkillRepository $skillR
     * @return Response
     * @Route("/discipline/{discipline}", name="discipline")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_STUDENT') or is_granted('ROLE_PARENT') or (is_granted('ROLE_TEACHER') and user.getTeacher().getDiscipline().contains(discipline))")
     */
    public function discipline(Discipline $discipline, DisciplineRepository $disciplineR, SkillRepository $skillR)
    {
        $page = 'discipline';
        $skills = $skillR->sumAllSkills($discipline);

        return $this->render('page_discipline.html.twig', [
            'page' => $page,
            'discipline' => $discipline,
            'skills' => $skills,
        ]);
    }

    /**
     * @param $discipline
     * @param SkillRepository $skillR
     * @param $student
     * @return Response
     * @Route("/{student}/discipline/{discipline}", name="student_discipline")
     * @Security("is_granted('ROLE_ADMIN') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student)) or (is_granted('ROLE_TEACHER') and user.getTeacher().getDiscipline().contains(discipline))")
     */
    public function studentDiscipline(Student $student, Discipline $discipline, SkillRepository $skillR)
    {
        $page = 'discipline';
        $skills = $skillR->sumAllSkills($discipline);

        /*$disciplineEntity = str_replace("-", " ", $discipline);
        $disciplineName = $disciplineR->findDisciplineBy($disciplineEntity);
        $skills = $skillR->sumAllSkills($disciplineEntity);*/

        return $this->render('page_discipline.html.twig', [
            'page' => $page,
            'discipline' => $discipline,
            'skills' => $skills,
            'student' => $student
        ]);
    }
}
