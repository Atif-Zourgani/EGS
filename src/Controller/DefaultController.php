<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/", name="home")
     */
    public function home()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') && $this->getUser()->getPasswordChangedAt() === null) {
            return $this->redirectToRoute('fos_user_change_password');
        } else {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_STUDENT')) {
                $id = $this->getUser()->getStudent()->getId();
                return $this->redirectToRoute('student_profile', ['student' => $id]);
            } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_PARENT')) {
                $students = $this->getUser()->getStudentParent()->getStudent();
                $student = $students[0];
                $studentId = $student->getid();
                $firstname = $student->getFirstname();
                $lastname = $student->getLastname();
                return $this->redirectToRoute('student', [
                    'student' => $studentId,
                    'firstname' => $firstname,
                    'lastname' => $lastname
                ]);
            } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('all_students');
            } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_TEACHER')) {
                return $this->redirectToRoute('select_call');
            }
        }

        return $this->redirectToRoute('fos_user_security_login');
    }
}