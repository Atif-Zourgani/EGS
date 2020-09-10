<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\SearchTeacherType;
use App\Repository\TeacherRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @param TeacherRepository $teacherR
     * @return Response
     * @Route("/intervenants", name="teachers")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function allTeachers(TeacherRepository $teacherR)
    {
        $page = 'all_teachers';

        $teacher = new Teacher();

        $form = $this->createForm(SearchTeacherType::class, $teacher);

        $result = $teacherR->findAll();

        return $this->render('page_all_teachers.html.twig', [
            'page' => $page,
            'teachers' => $result,
            "formSearch" => $form->createView()
        ]);
    }

    /**
     * @param TeacherRepository $teacherR
     * @param Request $request
     * @return Response
     * @Route("/intervenants/recherche", name="teacher_search")
     */
    public function teacherSearchAction(TeacherRepository $teacherR, Request $request)
    {
        $query = $request->get('searchText');
        $teachers = $teacherR->findByQuery($query);

        if(!$teachers) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealTeachers($teachers);
        }

        return new JsonResponse($result); // translate to json
    }

    public function getRealTeachers($teachers)
    {
        foreach ($teachers as $teacher){
            $realTeachers[$teacher->getId()] = [
                $teacher->getFullname()
            ];
        }
        return $realTeachers;
    }
}
