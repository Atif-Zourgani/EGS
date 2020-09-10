<?php

namespace App\Controller;

use App\Repository\RollCallRepository;
use App\Repository\StudentCallRepository;
use App\Repository\StudentReliabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class BackUpController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/admin/sauvegarder/donnees", name="back_up")
     */
    public function backUpIndex()
    {
        $page = 'back_up';

        return $this->render('back_up/index.html.twig', [
            'page' => $page
        ]);
    }


    /**
     * Download all roll calls of the year
     *
     * @param StudentCallRepository $studentCallR
     * @return RedirectResponse
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/admin/sauvegarder/appels", name="download_roll_calls")
     */
    public function downloadRollCalls(StudentCallRepository $studentCallR)
    {
        $rollCalls = $studentCallR->findAllRollCallsOfTheYear();

        $rollCallsArray = [];

        foreach ($rollCalls as $call) {
            $firstname['firstname'] = $call['firstname'];
            $lastname['lastname'] = $call['lastname'];
            $section['section'] = $call['section'];
            $date['date'] = $call['createdAt']->format('d m Y');
            $halfDay['halfDay'] = $call['halfDay'];
            $status['status'] = $call['status'];

            if ($call['halfDay'] == 'am') {
                $halfDay['halfDay'] = 'matin';
            } elseif ($call['halfDay'] == 'pm') {
                $halfDay['halfDay'] = 'Après-midi';
            }

            if ($call['status'] == 'justified') {
                $status['status'] = 'Absence justifiée';
            } elseif ($call['status'] == 'absent') {
                $status['status'] = 'Absent';
            } elseif ($call['status'] == 'late') {
                $status['status'] = 'En retard';
            }

            $infos = $lastname + $firstname + $section + $date + $halfDay + $status;

            array_push($rollCallsArray, $infos);
        }

        $headers = ["Nom", "Prénom", "Classe", "Date", "Demi-journée", "Statut"];

        $today = new \DateTime('Europe/Paris');
        $todayShort = $today->format("d.m.y-G.i");
        $filename = "appels_" . $todayShort;

        // create new file for back up
        $fh = fopen('../sauvegarde/appels/' . $filename .'.csv', 'w');
        fputcsv($fh, $headers);

        foreach ($rollCallsArray as $call) {
            fputcsv($fh, $call);
        }

        fclose($fh);

        if (file_exists('../sauvegarde/appels/' . $filename .'.csv')) {
            $this->addFlash(
                'rollCallsDownload',
                'Le fichier "' . $filename . '" a bien été sauvegardé.'
            );
        } else {
            $this->addFlash(
                'errorBackup',
                'Un problème est survenu, le fichier "' . $filename . '" n\'a pas été sauvegardé.'
            );
        }


        return $this->redirectToRoute('back_up');
    }


    /**
     * Delete all roll calls of the year
     *
     * @param EntityManagerInterface $entityManager
     * @param StudentCallRepository $studentCallR
     * @param RollCallRepository $rollCallR
     * @return RedirectResponse
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/admin/supprimer/appels", name="delete_roll_calls")
     */
    public function deleteRollCalls(EntityManagerInterface $entityManager, StudentCallRepository $studentCallR, RollCallRepository $rollCallR)
    {
        $rollCalls = $rollCallR->findAll();
        $studentCalls = $studentCallR->findAll();

        foreach ($rollCalls as $rollCall) {
            $entityManager->remove($rollCall);
        }

        foreach($studentCalls as $studentCall) {
            $entityManager->remove($studentCall);
        }

        $entityManager->flush();

        $this->addFlash(
            'rollCallsDeleted',
            'Les appels ont bien été supprimés.'
        );

        return $this->redirectToRoute('back_up');
    }


    /**
     * Download all student's incidents of the year
     *
     * @param StudentReliabilityRepository $studentReliabilityR
     * @return RedirectResponse
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/admin/sauvegarder/incidents", name="download_student_incidents")
     */
    public function downloadStudentIncidents(StudentReliabilityRepository $studentReliabilityR)
    {
        $dateIncidents = $studentReliabilityR->findAllIncidentsToDownload();

        $incidentsArray = [];

        foreach ($dateIncidents as $incident) {

            foreach ($incident['incident'] as $singleIncident) {
                $firstname['firstname'] = $incident['student']['firstname'];
                $lastname['lastname'] = $incident['student']['lastname'];
                $section['section'] = $incident['student']['section']['name'];
                $date['date'] = $incident['createdAt']->format('d m Y');
                $test['test'] = $singleIncident['name'];
                $points['points'] = $singleIncident['points'];
                $infos = $lastname + $firstname + $section + $date + $test + $points;
                array_push($incidentsArray, $infos);
            }
        }

        $headers = ["Nom", "Prénom", "Classe", "Date", "Incident", "Points"];

        $today = new \DateTime('Europe/Paris');
        $todayShort = $today->format("d.m.y-G.i");
        $filename = "incidents_" . $todayShort;

        // create new file for back up
        $fh = fopen('../sauvegarde/incidents/' . $filename .'.csv', 'w');
        fputcsv($fh, $headers);

        foreach ($incidentsArray as $incident) {
            fputcsv($fh, $incident);
        }

        fclose($fh);

        if (file_exists('../sauvegarde/incidents/' . $filename .'.csv')) {
            $this->addFlash(
                'studentsIncidentsDownload',
                'Le fichier "' . $filename . '" a bien été sauvegardé.'
            );
        } else {
            $this->addFlash(
                'errorBackup',
                'Un problème est survenu, le fichier "' . $filename . '" n\'a pas été sauvegardé.'
            );
        }

        return $this->redirectToRoute('back_up');
    }


    /**
     * Delete all students incidents of the year
     *
     * @param EntityManagerInterface $entityManager
     * @param StudentReliabilityRepository $studentReliabilityR
     * @return RedirectResponse
     *
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     * @Route("/admin/supprimer/incidents", name="delete_students_incidents")
     */
    public function deleteStudentsIncidents(EntityManagerInterface $entityManager, StudentReliabilityRepository $studentReliabilityR)
    {
        $studentIncidents = $studentReliabilityR->findAll();

        foreach ($studentIncidents as $studentIncident) {
            $entityManager->remove($studentIncident);
        }

        $entityManager->flush();

        $this->addFlash(
            'rollCallsDeleted',
            'Les incidents des étudiants ont bien été supprimés.'
        );

        return $this->redirectToRoute('back_up');
    }
}
