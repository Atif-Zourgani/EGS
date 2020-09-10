<?php

namespace App\Controller;

use App\Entity\RollCall;
use App\Entity\StudentCall;
use App\Entity\StudentReliability;
use App\Form\RollCallType;
use App\Repository\IncidentRepository;
use App\Repository\RollCallRepository;
use App\Repository\SectionRepository;
use App\Repository\SemesterRepository;
use App\Repository\StudentCallRepository;
use App\Repository\StudentReliabilityRepository;
use App\Repository\StudentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;


class RollCallController extends AbstractController
{
    /**
     * Return homepage of roll calls
     *
     * @param SectionRepository $sectionR
     * @return Response
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/appel", name="select_call")
     */
    public function selectCall(SectionRepository $sectionR)
    {
        $page = 'select_call';

        $sections = $sectionR->findAllSectionsOrderByName();

        return $this->render('call/select_call.html.twig', [
            'page' => $page,
            'sections' => $sections,
        ]);
    }

    /**
     * Send a mail to admin if a student has too much delays or absences
     *
     * @param StudentCall $studentCall
     * @param StudentCallRepository $studentCallR
     * @param \Swift_Mailer $mailer
     */
    private function sendCallMail(StudentCall $studentCall, StudentCallRepository $studentCallR, \Swift_Mailer
    $mailer)
    {
        $status = $studentCall->getStatus();
        $student = $studentCall->getStudent();
        $id = $student->getId();
        $absences = $studentCallR->findAbsencesByStudent($id);
        $delays = $studentCallR->findDelaysByStudent($id);
        // get abs and delays + the new abs or delay (which is not flush yet)
        $countDelays = (count($delays) + 1);
        $countAbsences = (count($absences) + 1);

        $message = (new \Swift_Message());
        if ($status == 'absent') {
            $message->setSubject('Trop d\'absences');
        } elseif ($status == 'late') {
            $message->setSubject('Trop de retards');
        }

        $message
            ->setFrom(['pedagogie@egs.school' => 'Pédagogie'])
            ->setTo('pedagogie@egs.school')
            ->setBody(
                $this->renderView(
                    'call/_call_mail.html.twig', [
                        'student' => $student,
                        'absences' => $countAbsences,
                        'delays' => $countDelays,
                        'status' => $status
                    ]
                ),
                'text/html'
            );

        // if status is absent or late and sum of abs/lateness is just before a modulo of 5.
        if (($status == 'absent') and (($countAbsences !== 0) and (($countAbsences + 1) % 5 == 0)))
        {
            $mailer->send($message);
        }

        if (($status == 'late') and (($countDelays !== 0) and (($countDelays + 1) % 5  == 0)))
        {
            $mailer->send($message);
        }
    }

    /**
     * Return student's current points
     *
     * @param $student
     * @param SemesterRepository $semesterR
     * @param IncidentRepository $incidentR
     * @return array
     */
    private function studentCurrentPoints($student, SemesterRepository $semesterR, IncidentRepository $incidentR)
    {
        $semesterId = $semesterR->findCurrentSemester();
        $reliabilityPoints = $incidentR->incidentPointsBy($student->getId(), $semesterId);

        $points = 20;
        $currentPoints = [];

        if (empty($reliabilityPoints)) {
            array_push($currentPoints, $points);
        } else {
            for($i = 0; $i < count($reliabilityPoints); ++$i) {
                foreach ($reliabilityPoints[$i] as $value) {
                    $points += $value;
                    array_push($currentPoints, $points);
                }
            }
        }

        return $currentPoints;
    }


    /**
     * Add an incident for a student after roll call if status is absent or late
     *
     * @param StudentCall $studentCall
     * @param SemesterRepository $semesterR
     * @param $status
     * @param EntityManagerInterface $entityManager
     * @param StudentCallRepository $studentCallR
     * @param IncidentRepository $incidentR
     * @param \Swift_Mailer $mailer
     */
    private function addIncident(StudentCall $studentCall, SemesterRepository $semesterR, $status, EntityManagerInterface $entityManager, StudentCallRepository $studentCallR, IncidentRepository $incidentR, \Swift_Mailer $mailer)
    {
        $student = $studentCall->getStudent();
        $teacher = $studentCall->getRollCall()->getTeacher();
        $team = $studentCall->getRollCall()->getTeam();

        $absences = $studentCallR->findAbsencesByStudent($student->getId());
        $delays = $studentCallR->findDelaysByStudent($student->getId());
        $countDelays = (count($delays) + 1);
        $countAbsences = (count($absences) + 1);

        $currentPoints = $this->studentCurrentPoints($student, $semesterR, $incidentR);

        $studentIncident = new StudentReliability($student, $teacher, $team);

        if ( (($countAbsences !== 0) and ($countAbsences % 5 == 0)) or (($countDelays !== 0) and ($countDelays % 5  == 0)) ){
            if ($status == 'absent' and ($countAbsences !== 0) and ($countAbsences % 5 == 0)) {
                $incident = $incidentR->findOneBy(['id' => 2]);
                $studentIncident->addIncident($incident);
                $studentIncident->setStudent($student);
                $entityManager->persist($studentIncident);

            } elseif ($status == 'late' and ($countDelays !== 0) and ($countDelays % 5  == 0)) {
                $incident = $incidentR->findOneBy(['id' => 1]);
                $studentIncident->addIncident($incident);
                $studentIncident->setStudent($student);
                $entityManager->persist($studentIncident);
            }


            $message = (new \Swift_Message('Nouvel incident'))
                ->setFrom(['pedagogie@egs.school' => 'Pédagogie'])
                ->setTo($student->getEmail());

            if ($status == 'absent' and ($countAbsences !== 0) and ($countAbsences % 5 == 0)) {
                $lastCurrentPoints = end($currentPoints) - 5;

                $message->setBody(
                    $this->renderView(
                        'incidents/_incident_mail.html.twig', [
                            'student' => $student,
                            'incident' => $studentIncident,
                            'points' => $lastCurrentPoints,
                            'type' => 'negative'
                        ]
                    ),
                    'text/html'
                );

                $mailer->send($message);

            } elseif ($status == 'late' and ($countDelays !== 0) and ($countDelays % 5  == 0)) {
                $lastCurrentPoints = end($currentPoints) - 3;

                $message->setBody(
                    $this->renderView(
                        'incidents/_incident_mail.html.twig', [
                            'student' => $student,
                            'incident' => $studentIncident,
                            'points' => $lastCurrentPoints,
                            'type' => 'negative'
                        ]
                    ),
                    'text/html'
                );

                $mailer->send($message);
            }


        }
    }

    /**
     * Remove an incident for a student after roll call if an absence is justified
     *
     * @param StudentCall $studentCall
     * @param SemesterRepository $semesterR
     * @param EntityManagerInterface $entityManager
     * @param StudentCallRepository $studentCallR
     * @param IncidentRepository $incidentR
     * @param \Swift_Mailer $mailer
     * @param StudentReliabilityRepository $studentReliabilityR
     */
    private function removeAbsenceIncident(StudentCall $studentCall, SemesterRepository $semesterR, EntityManagerInterface $entityManager, StudentCallRepository $studentCallR, IncidentRepository $incidentR, \Swift_Mailer $mailer,
    StudentReliabilityRepository $studentReliabilityR)
    {

        $student = $studentCall->getStudent();
        $absences = $studentCallR->findAbsencesByStudent($student->getId());
        $countAbsences = count($absences);
        $lastStudentCallIncident = $studentReliabilityR->findLastStudentIncident($student->getId(), 2);

        if (!empty($lastStudentCallIncident) and ($countAbsences !== 0) and ($countAbsences % 5 == 0)) {

            $entityManager->remove($lastStudentCallIncident);

            if ($studentCall->getStatus() == 'justified') {
                $currentPoints = $this->studentCurrentPoints($student, $semesterR, $incidentR);
                $lastCurrentPoints = end($currentPoints) + 5;

                $message = (new \Swift_Message('Récupération de points'))
                    ->setFrom(['pedagogie@egs.school' => 'Pédagogie'])
                    ->setTo($student->getEmail())
                    ->setBody(
                        $this->renderView(
                            'incidents/_remove_incident_mail.html.twig', [
                                'student' => $student,
                                'points' => $lastCurrentPoints
                            ]
                        ),
                        'text/html'
                    );
                $mailer->send($message);
            }

        }

    }

    /**
     * Remove an incident (lateness) for a student after roll call if a teacher made a mistake
     *
     * @param StudentCall $studentCall
     * @param EntityManagerInterface $entityManager
     * @param StudentCallRepository $studentCallR
     * @param IncidentRepository $incidentR
     * @param \Swift_Mailer $mailer
     * @param StudentReliabilityRepository $studentReliabilityR
     */
    private function removeLatenessIncident(StudentCall $studentCall, EntityManagerInterface $entityManager, StudentCallRepository $studentCallR, IncidentRepository $incidentR, \Swift_Mailer $mailer,
                                           StudentReliabilityRepository $studentReliabilityR)
    {
        $student = $studentCall->getStudent();
        $delays = $studentCallR->findDelaysByStudent($student->getId());
        $countDelays = count($delays);
        $lastStudentCallIncident = $studentReliabilityR->findLastStudentIncident($student->getId(), 1);

        if (!empty($lastStudentCallIncident) and ( ($countDelays !== 0) and ($countDelays % 5 == 0)) ) {
            $entityManager->remove($lastStudentCallIncident);
        }
    }


    /**
     * Add roll call for a section and date with all studentCalls
     * and send emails to student if too much lateness/absences
     * and add an incident if status is 'late'
     *
     * @param Request $request
     * @param ObjectManager $m
     * @param SectionRepository $sectionR
     * @param StudentRepository $studentR
     * @param StudentCallRepository $studentCallR
     * @param \Swift_Mailer $mailer
     * @param RollCallRepository $rollCallR
     * @param IncidentRepository $incidentR
     * @param EntityManagerInterface $entityManager
     * @param SemesterRepository $semesterR
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/appel/classe/", name="call")
     */
    public function sectionToCall(Request $request, ObjectManager $m, SectionRepository $sectionR, StudentRepository
    $studentR, StudentCallRepository $studentCallR, \Swift_Mailer $mailer, RollCallRepository $rollCallR, IncidentRepository $incidentR,
    EntityManagerInterface $entityManager, SemesterRepository $semesterR)
    {
        $page = 'create_call';

        $date = $request->query->get('date');
        $sectionName = $request->query->get('section');
        $halfDay = $request->query->get('halfDay');

        $section = $sectionR->findSectionBy($sectionName);

        $dateTime = New \DateTime($date);
        $dateTime->format('Y-m-d');

        $rollCall = $rollCallR->findAll();

        foreach ($rollCall as $call) {
            if (($call->getHalfDay() == $halfDay) and ($call->getSection() == $section) and ($call->getCreatedAt() == $dateTime)) {
                $this->addFlash('fail', 'Un appel a déjà été enregistré pour cette classe.');
                return $this->redirectToRoute('select_call');
            }
        }

        $students = $studentR->findStudentBySection($sectionName);
        $user = $this->getUser();
        $teacher = $user->getTeacher();
        $team = $user->getTeam();

        $call = new RollCall();

        foreach ($students as $student) {
            $studentCall = new StudentCall();
            $studentCall->setStatus('present')
                        ->setStudent($student);

            $call->addStudentCall($studentCall);
        }

        $form = $this->createForm(RollCallType::class, $call);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $call->setSection($section)
                ->setTeacher($teacher)
                ->setTeam($team)
                ->setHalfDay($halfDay)
                ->setCreatedAt($dateTime);
            foreach($call->getStudentCalls() as $studentCall) {
                $studentCall->setRollCall($call);
                $m->persist($studentCall);

                /* CORONA REMOVE */
                /*$status = $studentCall->getStatus();
                if ($status == 'absent' or $status == 'late') {
                    $this->sendCallMail($studentCall, $studentCallR, $mailer);
                    $this->addIncident($studentCall, $semesterR, $status, $entityManager, $studentCallR, $incidentR, $mailer);
                }*/
            }
            $m->persist($call);
            $m->flush();

            $this->addFlash('success', 'L\'appel a bien été enregistré.');
            return $this->redirectToRoute('select_call');

        }

        return $this->render('call/create_call.html.twig', [
            'page' => $page,
            'sectionName' => $sectionName,
            'halfDay' => $halfDay,
            'students' => $students,
            'section' => $section,
            'date' => $date,
            'formCall' => $form->createView()
        ]);
    }


    /**
     * Update a roll call and send an email if too much lateness or absences
     * and remove last incident for absence for a student if an absence is justified
     *
     * @param $id
     * @param ObjectManager $m
     * @param Request $request
     * @param RollCallRepository $rollCallR
     * @param StudentCallRepository $studentCallR
     * @param \Swift_Mailer $mailer
     * @param EntityManagerInterface $entityManager
     * @param SemesterRepository $semesterR
     * @param IncidentRepository $incidentR
     * @param StudentReliabilityRepository $studentReliabilityR
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/modifier/appel/{id}", name="update_call")
     */
    public function updateRollCall($id, ObjectManager $m, Request $request, RollCallRepository $rollCallR,
                                   StudentCallRepository $studentCallR, \Swift_Mailer $mailer, EntityManagerInterface
                                   $entityManager, SemesterRepository $semesterR, IncidentRepository $incidentR, StudentReliabilityRepository $studentReliabilityR)
    {
        $page = 'update_call';

        $call = $rollCallR->findOneBy(['id' => $id]);

        $form = $this->createForm(RollCallType::class, $call);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($call->getStudentCalls() as $studentCall) {

                $studentCall->setRollCall($call);
                $m->persist($studentCall);

                $uow = $entityManager->getUnitOfWork();
                $uow->computeChangeSets();

                // if $studentCall was updated
                /* CORONA REMOVE */
                /*if ($uow->isEntityScheduled($studentCall)) {

                    $oldStatus = $uow->getEntityChangeSet($studentCall)['status'][0];

                    $newStatus = $studentCall->getStatus();

                    $this->sendCallMail($studentCall, $studentCallR, $mailer);

                    if ($oldStatus == 'absent') {
                        $this->removeAbsenceIncident($studentCall, $semesterR, $entityManager, $studentCallR, $incidentR, $mailer, $studentReliabilityR);
                    }

                    if ($oldStatus == 'late') {
                        $this->removeLatenessIncident($studentCall, $entityManager, $studentCallR, $incidentR, $mailer, $studentReliabilityR);
                    }

                    if ($newStatus == 'absent' or $newStatus == 'late') {
                        $this->addIncident($studentCall, $semesterR, $newStatus, $entityManager, $studentCallR, $incidentR, $mailer);
                    }
                }*/
            }

            $m->persist($call);
            $m->flush();
            $this->addFlash('success', 'L\'appel a bien été modifié.');
            return $this->redirectToRoute('select_call');
        }

        return $this->render('call/update_call.html.twig', [
            'page' => $page,
            'formCall' => $form->createView(),
            'call' => $call,
        ]);
    }


    /**
     * Display all roll calls for admins or roll calls of the day for teachers
     *
     * @param SectionRepository $sectionR
     * @param RollCallRepository $rollCallR
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/appels", name="display_calls")
     */
    public function displayRollCall(RollCallRepository $rollCallR, SectionRepository $sectionR)
    {
        $page = 'display_calls';

        $user = $this->getUser();

        $sections = $sectionR->findAllSectionsOrderByName();

        $dates = $rollCallR->displayDateCall();

        if ($user->getTeacher()) {
            $teacher = $user->getTeacher();
            $calls = $rollCallR->displayTeacherCalls($teacher);
        } else {
            $calls = $rollCallR->displayLastCalls();
        }

        return $this->render('call/display_calls.html.twig', [
            'page' => $page,
            'calls' => $calls,
            'sections' => $sections,
            'dates' => $dates
        ]);
    }

}
