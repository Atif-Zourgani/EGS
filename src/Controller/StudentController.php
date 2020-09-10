<?php

namespace App\Controller;


use App\Entity\Feed;
use App\Entity\Student;
use App\Entity\StudentParent;
use App\Entity\StudentReliability;
use App\Entity\User;
use App\Form\FeedType;
use App\Form\RollCallType;
use App\Form\SearchParentType;
use App\Form\SearchStudentType;
use App\Repository\CommentRepository;
use App\Repository\DisciplineCatRepository;
use App\Repository\DisciplineRepository;
use App\Repository\FeedRepository;
use App\Repository\IncidentRepository;
use App\Repository\LevelRepository;
use App\Repository\RollCallRepository;
use App\Repository\SemesterRepository;
use App\Repository\SkillRepository;
use App\Repository\StudentCallRepository;
use App\Repository\StudentParentRepository;
use App\Repository\StudentReliabilityRepository;
use App\Repository\StudentRepository;
use App\Repository\StudentSkillRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\FOSUserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class StudentController extends AbstractController
{

    /**
     * Display all students
     *
     * @param StudentRepository $studentR
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/etudiants", name="all_students")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function allStudents(StudentRepository $studentR)
    {
        $page = 'all_students';

        $student = new Student();

        $form = $this->createForm(SearchStudentType::class, $student);

        $result = $studentR->findAllStudents();

        return $this->render('page_all_students.html.twig', [
            'page' => $page,
            "students" => $result,
            "formSearch" => $form->createView()
        ]);
    }


    /**
     * Allow to find a student in search with a firstname, lastname or fullname
     *
     * @param StudentRepository $studentR
     * @param Request $request
     * @return JsonResponse
     * @Route("/etudiants/recherche", name="student_search")
     */
    public function studentSearchAction(StudentRepository $studentR, Request $request)
    {
        $query = $request->get('searchText');
        $students = $studentR->findByQuery($query);

        if(!$students) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealStudents($students);
        }

        return new JsonResponse($result); // translate to json
    }

    /**
     *  Find all students with label "elite"
     *
     * @param StudentRepository $studentR
     * @return JsonResponse
     *
     * @Route("/elite/recherche", name="elite_search")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function findElite(StudentRepository $studentR)
    {
        $students = $studentR->findElite();

        if(!$students) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealStudents($students);
        }

        return new JsonResponse($result); // translate to json
    }

    /**
     *  Find all students with label "gamer"
     *
     * @param StudentRepository $studentR
     * @return JsonResponse
     *
     * @Route("/gamer/recherche", name="gamer_search")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function findGamer(StudentRepository $studentR)
    {
        $students = $studentR->findGamer();

        if(!$students) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealStudents($students);
        }

        return new JsonResponse($result); // translate to json
    }

    /**
     *  Find all students with label "challenger"
     *
     * @param StudentRepository $studentR
     * @return JsonResponse
     *
     * @Route("/challenger/recherche", name="challenger_search")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function findChallenger(StudentRepository $studentR)
    {
        $students = $studentR->findChallenger();

        if(!$students) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealStudents($students);
        }

        return new JsonResponse($result); // translate to json
    }

    /**
     * @param $students
     * @return mixed
     */
    public function getRealStudents($students)
    {
        foreach ($students as $student){
            $realStudents[$student->getId()] = [
                $student->getFullname()
            ];
        }
        return $realStudents;
    }


    /**
     * @param $student
     * @param DisciplineRepository $disciplineR
     * @param DisciplineCatRepository $disciplineCatR
     * @param StudentRepository $studentR
     * @param CommentRepository $commentR
     * @param DisciplineCatRepository $categoryR
     * @param StudentCallRepository $studentCallR
     * @param SkillRepository $skillR
     * @param TeacherRepository $teacherR
     * @param IncidentRepository $incidentR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and student == user.getStudent()) or (is_granted('ROLE_PARENT') and user.getStudentParent().getStudent().contains(student))")
     *
     * @Route("/etudiant/{student}/{firstname}/{lastname}", name="student")
     *
     * @Route("/{student}/mon-profil", name="student_profile")
     */
    public function student(Student $student, DisciplineRepository $disciplineR, StudentRepository $studentR,
                            CommentRepository $commentR, DisciplineCatRepository $categoryR, TeacherRepository $teacherR, IncidentRepository $incidentR,
                            SkillRepository $skillR, StudentCallRepository $studentCallR, DisciplineCatRepository $disciplineCatR, SemesterRepository $semesterR,
                            FeedRepository $feedRepository, Request $request, \Swift_Mailer $mailer)
    {
        $page = 'student';

        $student = $studentR->findStudentBy($student);
        $user = $this->getUser();

        $disciplines = $disciplineR->findAll();
        $disciplinesAsc = $disciplineR->findAllAsc();
        $absences = $studentCallR->findAbsencesByStudent($student);
        $delays = $studentCallR->findDelaysByStudent($student);
        $attendance = $studentCallR->findAbsencesAndDelaysByStudent($student);
        $elite = $student->getElite();
        $challenger = $student->getChallenger();

        /*if ($user->getTeacher()) {
            $categories = $teacherC = $categoryR->findDisciplineCatByStudentSkillsAndTeacher($student, $user->getTeacher(), $teacherR);
        } else {
            $categories = $categoryR->findCategoryByDisciplineStudent($student);
        }*/

        $categories = $categoryR->findCategoryByDisciplineStudent($student);
        $studentDisciplines = $disciplineR->findDisciplineByStudentSkills($student);
        $studentDisciplinesCat = $disciplineCatR->findDisciplineCatByStudentSkills($student);

        /*if ($user->getTeacher()) {
            $studentDisciplines = $disciplineR->findDisciplineByStudentSkillsAndTeacher($student, $user->getTeacher()->getId(), $teacherR);
            $studentDisciplinesCat = $disciplineCatR->findDisciplineCatByStudentSkillsAndTeacher($student, $user->getTeacher()->getId(), $teacherR);
        } else {
            $studentDisciplines = $disciplineR->findDisciplineByStudentSkills($student);
            $studentDisciplinesCat = $disciplineCatR->findDisciplineCatByStudentSkills($student);
        }*/

        $comments = $commentR->findCommentByStudent($student);
        $teachers = $teacherR->findTeacherByStudentComment($student);

        $semesters = $semesterR->findLastsSemesters();
        $semesterId = $semesterR->findCurrentSemester();

        $studentIncidents = $this->getStudentIncidentPoints($student, $incidentR, $semesterId);
        $currentPoints = $studentIncidents["currentPoints"];

        // get an array of student's skills total foreach level's discipline
        $disciplineInfos = [];
        $i = 0;
        foreach ($studentDisciplines as $discipline) {
            foreach ($discipline['disciplineLevels'] as $disciplineLevel) {
                // Get all skills where discipline_level_id = $disciplineLevel['id']
                $skillsTotal = $skillR->findBy(['disciplineLevel' => $disciplineLevel['id']]);

                $disciplineInfos[$i]['id'] = $disciplineLevel['id'];
                $disciplineInfos[$i]['skillsTotal'] = count($skillsTotal);
                $i++;
            }
        }

        $feed = new Feed();
        $form = $this->createForm(FeedType::class, $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            if ($this->isGranted('ROLE_ADMIN')) {
                $team = $user->getTeam();
                $feed->setTeam($team);
            } elseif ($this->isGranted('ROLE_TEACHER')) {
                $teacher = $user->getTeacher();
                $feed->setTeacher($teacher);
            }

            if ($form->getData()->getSharedToAll() == null) {
                $feed->setStudent($student);
            }

            $entityManager->persist($feed);
            $entityManager->flush();

            $comment = $form->getData()->getComment();

            $message = (new \Swift_Message('Nouveau message'))
                ->setFrom(['pedagogie@egs.school' => 'Pédagogie'])
                ->setTo($student->getEmail())
                ->setBody(
                    $this->renderView(
                        'feed/_feed_mail.html.twig', ['comment' => $comment]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirect($request->getUri());
        }

        $feeds = $feedRepository->findByStudentAndAll($student);

        if ($user->getTeacher()) {
            return $this->render('student/page_student.html.twig', [
                'page' => $page,
                'student' => $student,
                'studentDisciplines' => $studentDisciplines,
                'studentDisciplinesCat' => $studentDisciplinesCat,
                'disciplineInfos' => $disciplineInfos,
                'disciplines' => $disciplines,
                'disciplinesAsc' => $disciplinesAsc,
                'categories' => $categories,
                'teachers' => $teachers,
                'teacherD' => $teacherR->findDisciplinesByTeacher($user->getTeacher()),
                'studentIncidents' => $studentIncidents,
                'comments' => $comments,
                'absences' => $absences,
                'attendance' => $attendance,
                'delays' => $delays,
                'currentPoints' => $currentPoints,
                'semesters' => $semesters,
                'elite' => $elite,
                'challenger' => $challenger,
                'form' => $form->createView(),
                'feeds' => $feeds
            ]);

        } else {
            return $this->render('student/page_student.html.twig', [
                'page' => $page,
                'student' => $student,
                'studentDisciplines' => $studentDisciplines,
                'studentDisciplinesCat' => $studentDisciplinesCat,
                'disciplineInfos' => $disciplineInfos,
                'disciplines' => $disciplines,
                'disciplinesAsc' => $disciplinesAsc,
                'categories' => $categories,
                'teachers' => $teachers,
                'studentIncidents' => $studentIncidents,
                'comments' => $comments,
                'absences' => $absences,
                'attendance' => $attendance,
                'delays' => $delays,
                'currentPoints' => $currentPoints,
                'semesters' => $semesters,
                'elite' => $elite,
                'challenger' => $challenger,
                'form' => $form->createView(),
                'feeds' => $feeds
            ]);
        }

    }

    /**
     * @Route("/flux/{id}/{student}", name="feed_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Feed $feed, Student $student): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feed->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($feed);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student', [
            'student' => $student->getId(),
            'firstname' => $student->getFirstname(),
            'lastname' => $student->getLastname()
        ]);
    }

    /**
     * @param $id
     * @param $semesterId
     * @param IncidentRepository $incidentR
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER') or (is_granted('ROLE_STUDENT') and id == user.getStudent().getId()) or (is_granted('ROLE_PARENT') and id == user.getStudentParent().getStudent().getId())")
     * @Route("/studentpoints/{id}/{semesterId}", name="studentPoints")
     */
    public function studentIncidentPoints($id, $semesterId, IncidentRepository $incidentR)
    {
        $studentIncidentPoints = $this->getStudentIncidentPoints($id, $incidentR, $semesterId);
        return new JsonResponse($studentIncidentPoints);
    }

    private function getStudentIncidentPoints($id, IncidentRepository $incidentR, $semesterId)
    {
        $reliabilityPoints = $incidentR->incidentPointsBy($id, $semesterId);

        // get an array of student's reliability points, added as we go along
        $points = 20;
        $currentPoints = [];
        for($i = 0; $i < count($reliabilityPoints); ++$i) {
            foreach ($reliabilityPoints[$i] as $value) {
                $points += $value;
                array_push($currentPoints, $points);
            }
        }

        // array with date + total incident's points for this date
        $studentIncidents = $incidentR->findIncidentBy($id, $semesterId);

        $incidentDetails = [];
        foreach ($studentIncidents as $element) {
            $incidentDetails[$element['date']][] = $element;
        }

        $incidentsByDate = [];
        foreach (array_values($incidentDetails) as $key => $studentIncident) {
            $incidentArray = [];

            $sumPoints = 0;
            for($i = 0; $i < count($studentIncident); ++$i) {
                $date = ['date' => $studentIncident[$i]['date']];
                $incidentDetails = ['incident' => $studentIncident[$i]['name'] . ' (' . $studentIncident[$i]['points'] . ')'];
                $teacher = $studentIncident[$i]['teacher'];
                $team = $studentIncident[$i]['team'];

                $sumPoints += $studentIncident[$i]['points'];
                array_push($incidentArray, $incidentDetails);
            }

            array_push($incidentsByDate, ['incidents' => $incidentArray] + $date + ['currentPoints' => $currentPoints[$key]] + ['sumPoints' => $sumPoints] + ['teacher' => $teacher] + ['team' => $team]);
        }

        return [
            "incidents" => $incidentsByDate,
            "currentPoints" => $currentPoints,
        ];
    }

    /**
     * Display all students' parents
     *
     * @param StudentParentRepository $parentR
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     * @Route("/parents", name="parents")
     */
    public function studentParent(StudentParentRepository $parentR)
    {
        $page = 'all_parents';

        $parent = new StudentParent();

        $form = $this->createForm(SearchParentType::class, $parent);

        $result = $parentR->findAllParents();

        return $this->render('page_all_parents.html.twig', [
            'page' => $page,
            'parents' => $result,
            "formSearch" => $form->createView()
        ]);
    }

    /**
     * Allow to find a parent in search with a firstname, lastname, fullname, or children's name
     *
     * @param StudentParentRepository $parentR
     * @param Request $request
     * @return Response
     * @Route("/parents/recherche", name="parent_search")
     */
    public function parentSearchAction(StudentParentRepository $parentR, Request $request)
    {
        $query = $request->get('searchText');
        $parents = $parentR->findByQuery($query);

        if(!$parents) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealParents($parents);
        }

        return new JsonResponse($result); // translate to json
    }

    public function getRealParents($parents)
    {
        foreach ($parents as $parent){
            $realParents[$parent->getId()] = [
                $parent->getFullname()
            ];
        }
        return $realParents;
    }

    /**
     * Add "Elite" label to a student
     *
     * @param Student $student
     * @param StudentRepository $studentR
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/elite/{student}", name="elite")
     */
    public function elite($student, StudentRepository $studentR, EntityManagerInterface $entityManager)
    {

        $studentE = $studentR->findOneBy(['id' => $student]);

        if ($studentE->getElite(1)) {
            $studentE->setElite(0);
        } else {
            $studentE->setElite(1);

            if ($studentE->getChallenger(1)) {
                $studentE->setChallenger(0);
            }
        }

        $entityManager->persist($studentE);

        $entityManager->flush();

        return $this->redirectToRoute('student_profile', ['student' => $student]);
    }

    /**
     * Add "Challenger" label to a student
     *
     * @param Student $student
     * @param StudentRepository $studentR
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/challenger/{student}", name="challenger")
     */
    public function challenger($student, StudentRepository $studentR, EntityManagerInterface $entityManager)
    {

        $studentE = $studentR->findOneBy(['id' => $student]);

        if ($studentE->getChallenger(1)) {
            $studentE->setChallenger(0);
        } else {
            $studentE->setChallenger(1);

            if ($studentE->getElite(1)) {
                $studentE->setElite(0);
            }
        }

        $entityManager->persist($studentE);

        $entityManager->flush();

        return $this->redirectToRoute('student_profile', ['student' => $student]);
    }

    /**
     * Add "Gamer" label to a student
     *
     * @param Student $student
     * @param StudentRepository $studentR
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/gamer/{student}", name="gamer")
     */
    public function gamer($student, StudentRepository $studentR, EntityManagerInterface $entityManager)
    {

        $studentE = $studentR->findOneBy(['id' => $student]);

        if ($studentE->getChallenger(1)) {
            $studentE->setChallenger(0);
        } elseif ($studentE->getElite(1)) {
            $studentE->setElite(0);
        }

        $entityManager->persist($studentE);

        $entityManager->flush();

        return $this->redirectToRoute('student_profile', ['student' => $student]);
    }

}