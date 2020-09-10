<?php

namespace App\Controller;

use App\Repository\GradeRepository;
use App\Repository\IncidentRepository;
use App\Repository\RollCallRepository;
use App\Repository\SemesterRepository;
use App\Repository\StudentCallRepository;
use App\Repository\StudentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    /**
     * Method to get all students with reliability >= 20 pts.
     *
     * @param StudentRepository $studentR
     * @param SemesterRepository $semesterR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIFFUSION') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/etudiants/fiabilite/20", name="students_best_reliability")
     */
    public function studentsBestReliability(SemesterRepository $semesterR, StudentRepository $studentR)
    {
        $semesterId = $semesterR->findCurrentSemester();
        $points = 20;

        $students = $studentR->findStudentByBestReliabilityPoints($semesterId, $points);

        return $this->render('statistics/students_best_reliability.html.twig', [
            'students' => $students
        ]);
    }

    /**
     * Method to get all students with reliability < 15 pts.
     *
     * @param StudentRepository $studentR
     * @param SemesterRepository $semesterR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIFFUSION') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/etudiants/fiabilite/15", name="students_bad_reliability")
     */
    public function studentsBadReliability(StudentRepository $studentR, SemesterRepository $semesterR)
    {
        $semesterId = $semesterR->findCurrentSemester();
        $points = 15;

        $students = $studentR->findStudentByBadReliabilityPoints($semesterId, $points);

        return $this->render('statistics/students_bad_reliability.html.twig', [
            'students' => $students
        ]);
    }

    /**
     * Method to get percentage by section and section's grade.
     *
     * @param GradeRepository $gradeR
     * @param RollCallRepository $rollCallR
     * @param StudentCallRepository $studentCallR
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_DIFFUSION') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/taux-de-presence", name="attendance_percentage")
     */
    public function attendancePercentage(GradeRepository $gradeR, RollCallRepository $rollCallR,
                                         StudentCallRepository $studentCallR)
    {
        $grades = $gradeR->findAllGradesWithRollCall();

        $allGrades = [];

        foreach ($grades as $grade) {

            $sections = $grade['sections'];
            $sectionInfos = [];
            $totalAttendance = 0;
            $totalAttendance2 = 0;
            $totalRollCalls = 0;
            $totalRollCalls2 = 0;


            foreach ($sections as $section) {

                $lastRollCallDate = $studentCallR->findLastRollCallDateBy($section['id']);

                $attendance = $rollCallR->findAttendanceBySection($section['id'], 1);
                $rollCall = $rollCallR->findAllRollCallBySection($section['id'], 1);

                $attendance2 = $rollCallR->findAttendanceBySection($section['id'], 2);

                $rollCall2 = $rollCallR->findAllRollCallBySection($section['id'], 2);

                $percentage['percentage'] = $attendance['attendance'] / $rollCall['calls'] * 100;

                if ($attendance2['attendance'] > 0 and $rollCall2['calls'] > 0) {
                    $percentage2['percentage'] = $attendance2['attendance'] / $rollCall2['calls'] * 100;
                    $array = ['lastRollCall' => $lastRollCallDate] + $attendance + $rollCall + $percentage + ['lastWeek' => $attendance2 + $rollCall2 + $percentage2];
                } else {
                    $array = ['lastRollCall' => $lastRollCallDate] + $attendance + $rollCall + $percentage;
                }
                array_push($sectionInfos, $array);

                // foreach section, add attendance's count, calls count and calculate percentage.
                $totalAttendance += $attendance['attendance'];
                $totalRollCalls += $rollCall['calls'];
                $totalPercentage = $totalAttendance / $totalRollCalls * 100;


                // SECOND TO LAST WEEK ATTENDANCE

                if ($attendance2['attendance'] > 0) {

                    $totalAttendance2 += $attendance2['attendance'];
                    $totalRollCalls2 += $rollCall2['calls'];
                    $totalPercentage2 = $totalAttendance2 / $totalRollCalls2 * 100;
                }
            }

            if ($attendance2['attendance'] > 0) {
                $gradeAttendance = $grade + ['gradeSections' => $sectionInfos] + ['totalAttendance' => $totalAttendance] +
                    ['totalRollCalls' => $totalRollCalls] + ['totalPercentage' => $totalPercentage] + ['totalLastPercentage' => $totalPercentage2];
            } else {
                $gradeAttendance = $grade + ['gradeSections' => $sectionInfos] + ['totalAttendance' => $totalAttendance] +
                    ['totalRollCalls' => $totalRollCalls] + ['totalPercentage' => $totalPercentage];
            }


            array_push($allGrades, $gradeAttendance);
        }


        return $this->render('statistics/attendance_percentage.html.twig', [
            'grades' => $allGrades
        ]);
    }
}
