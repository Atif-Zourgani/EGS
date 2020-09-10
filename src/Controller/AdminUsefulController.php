<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsefulController extends AbstractController
{
    /**
     * Page useful index
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/admin/utilitaires", name="useful")
     */
    public function usefulIndex()
    {
        $page = 'useful';

        return $this->render('admin_useful/page_useful.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * Return all students without user's account
     *
     * @param StudentRepository $studentR
     * @return Response
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/admin/etudiants-sans-utilisateur", name="students_without_account")
     */
    public function studentsWithoutAccount(StudentRepository $studentR) {

        $page = 'students_without_account';

        $students = $studentR->findStudentsWithoutAccount();

        return $this->render('admin_useful/new_student_user.html.twig', [
            'page' => $page,
            'students' => $students
        ]);
    }


    /**
     * Create new student's user
     *
     * @param StudentRepository $studentR
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/admin/creer-utilisateurs", name="create_students_account")
     */
    public function createStudentsAccount(StudentRepository $studentR, EntityManagerInterface $entityManager) {

        $qs = preg_replace("/(?<=^|&)(\w+)(?==)/", "$1[]", $_SERVER["QUERY_STRING"]);
        parse_str($qs, $param);

        foreach ($param['student'] as $student) {
            $studentEntity = $studentR->findOneBy(['id' => $student]);
            $email = $studentEntity->getEmail();
            $username = strtolower($studentEntity->getFirstname() . $studentEntity->getLastname());

            $search = explode(",", "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,-");
            $replace = explode(",", "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,");

            $user = new User();

            $user->setStudent($studentEntity);
            $user->setUsername(str_replace($search, $replace, $username));
            $user->setRoles(['ROLE_STUDENT']);
            $user->setEmail($email);
            $user->setPassword(md5('Ut65vTIjbWca'));
            $user->setEnabled(1);

            $entityManager->persist($user);

            $entityManager->flush();
        }

        return $this->redirectToRoute('students_without_account');
    }


    /**
     * List all students to disable
     *
     * @param StudentRepository $studentR
     * @return Response
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/admin/liste-etudiants", name="admin_list_students")
     */
    public function adminListStudents(StudentRepository $studentR)
    {
        $page = 'disable_students';

        $students = $studentR->findAllStudents();

        $studentsA = [];

        foreach ($students as $student)
        {
            $studentName = mb_strtoupper($student->getLastname()) . ' ' . $student->getFirstname();
            $studentSection = $student->getSection()->getShortname();

            $studentI = $studentName . ' ' . $studentSection;

            $studentA = [
                'student' => $studentI,
                'id' => $student->getId()
            ];

            array_push($studentsA, $studentA);
        }

        return $this->render('admin_useful/disable_student.html.twig', [
            'page' => $page,
            'students' => $studentsA
        ]);
    }


    /**
     * Disable students and user's accounts
     *
     * @param StudentRepository $studentR
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userR
     * @return Response
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/admin/desactiver-etudiants", name="disable_students")
     */
    public function disableStudents(StudentRepository $studentR, EntityManagerInterface $entityManager, UserRepository $userR)
    {
        $qs = preg_replace("/(?<=^|&)(\w+)(?==)/", "$1[]", $_SERVER["QUERY_STRING"]);
        parse_str($qs, $param);

        foreach ($param['student'] as $student) {

            $student = $studentR->findOneBy(['id' => $student]);
            $studentUser = $userR->findStudentUser($student->getId());

            $student->setEnabled(0);
            if ($student->getEnabled(1)) {
                $studentUser->setEnabled(0);
            }

            $entityManager->persist($student);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Les étudiants ont bien été désactivés');
        }

        return $this->redirectToRoute('admin_list_students');
    }
}
