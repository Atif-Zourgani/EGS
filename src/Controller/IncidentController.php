<?php

namespace App\Controller;

use App\Entity\StudentReliability;
use App\Form\StudentReliabilityType;
use App\Repository\IncidentRepository;
use App\Repository\SemesterRepository;
use App\Repository\StudentCallRepository;
use App\Repository\StudentReliabilityRepository;
use App\Repository\StudentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IncidentController extends AbstractController
{
    /**
     * @param $id
     * @param $type
     * @param Request $request
     * @param ObjectManager $m
     * @param StudentRepository $studentR
     * @param IncidentRepository $incidentR
     * @param StudentReliabilityRepository $studentReliabilityR
     * @param \Swift_Mailer $mailer
     * @param SemesterRepository $semesterR
     * @return RedirectResponse|Response
     *
     * @Route("/etudiant/{id}/incident/{type}", name="student_incident")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function studentIncidents($id, $type, Request $request, ObjectManager $m, StudentRepository $studentR, IncidentRepository $incidentR, StudentReliabilityRepository $studentReliabilityR, \Swift_Mailer $mailer, SemesterRepository $semesterR)
    {
        $page = 'student_incident';

        $student = $studentR->findOneBy(['id'=>$id]);
        $user = $this->getUser();
        $teacher = $user->getTeacher();
        $team = $user->getTeam();
        $studentReliability = $studentReliabilityR->reliabilityFindBy($id);

        $semesterId = $semesterR->findCurrentSemester();
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

        $studentIncident = new StudentReliability($student, $teacher, $team);

        $form = $this->createForm(StudentReliabilityType::class, $studentIncident, ['type' => $type]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $m->persist($studentIncident);
            $student->addReliability($studentIncident);
            $m->persist($student);
            $m->flush();

            $reliabilityPoints = $incidentR->incidentPointsBy($id, $semesterId);
            $points = 20;
            $currentPoints = [];
            for($i = 0; $i < count($reliabilityPoints); ++$i) {
                foreach ($reliabilityPoints[$i] as $value) {
                    $points += $value;
                    array_push($currentPoints, $points);
                }
            }

            $message = (new \Swift_Message('Nouvel incident'))
                ->setFrom(['pedagogie@egs.school' => 'Pédagogie'])
                ->setTo($student->getEmail())
                ->setBody(
                    $this->renderView(
                        'incidents/_incident_mail.html.twig', ['student' => $student, 'incident' => $studentIncident, 'points'
                        => end($currentPoints), 'type' => $type]
                    ),
                    'text/html'
                );
            $mailer->send($message);


            $this->addFlash(
                'succes',
                "L'incident a bien été enregistré."
            );

            return $this->redirectToRoute('student', ['student' => $student->getId(), 'firstname' => $student->getFirstname(), 'lastname' => $student->getLastname()]);
        }

        return $this->render('incidents/page_student_incidents.html.twig', [
            'page' => $page,
            'type' => $type,
            'student' => $student,
            'studentIncident' => $studentIncident,
            'currentPoints' => $currentPoints,
            'points' => end($currentPoints),
            'studentReliability' => $studentReliability,
            'incidentsForm' => $form->createView()
        ]);
    }

}
