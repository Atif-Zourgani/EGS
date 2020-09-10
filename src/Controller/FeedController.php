<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Form\FeedType2;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FeedController extends AbstractController
{
    /**
     * @Route("/flux", name="feed")
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function index(Request $request, \Swift_Mailer $mailer, StudentRepository $studentR)
    {
        $feed = new Feed();
        $form = $this->createForm(FeedType2::class, $feed);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            if ($this->isGranted('ROLE_ADMIN')) {
                $team = $user->getTeam();
                $feed->setTeam($team);
            } elseif ($this->isGranted('ROLE_TEACHER')) {
                $teacher = $user->getTeacher();
                $feed->setTeacher($teacher);
            }

            $entityManager->persist($feed);
            $entityManager->flush();

            $comment = $form->getData()->getComment();

            if ($form->getData()->getSharedToAll() === true) {
                $students = $studentR->findAllStudents();
                foreach($students as $student) {
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
                }
            } else {
                $student = $form->getData()->getStudent();
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
            }

            return $this->redirectToRoute('feed');
        }

        return $this->render('feed/form.html.twig', [
            'form' => $form->createView(),
            'page' => 'feed'
        ]);
    }
}
