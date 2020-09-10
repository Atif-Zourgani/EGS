<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\SearchSectionType;
use App\Repository\SectionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{

    /**
     * @param SectionRepository $sectionR
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/classes", name="all_sections")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     */
    public function allSections(SectionRepository $sectionR)
    {
        $page = 'all_sections';

        // instance de l'entité Section
        $section = new Section();

        // création de la représentation abstraite du formulaire selon de SearchSectionType créé
        $form = $this->createForm(SearchSectionType::class, $section);

        $result = $sectionR->findAllSections();

        // retourne la page des sections paginée avec le formulaire de recherche
        return $this->render('page_all_sections.html.twig', [
            'page' => $page,
            'sections' => $result,
            "formSearch" => $form->createView()
        ]);
    }

    /**
     * @param SectionRepository $sectionR
     * @param Request $request
     * @return JsonResponse
     * @Route("/classes/recherche", name="section_search")
     */
    public function sectionSearchAction(SectionRepository $sectionR, Request $request)
    {
        $query = $request->get('searchText');
        $entities = $sectionR->findByQuery($query);

        if(!$entities) {
            $result['entities']['error'] = "Aucun résultat trouvé pour cette recherche.";
        } else {
            $result['entities'] = $this->getRealEntities($entities);
        }

        return new JsonResponse($result); // translate to json
    }

    public function getRealEntities($entities)
    {
        foreach ($entities as $entity){
            $realEntities[$entity->getId()] = [
                $entity->getName()
            ];
        }
        return $realEntities;
    }

    /**
     * @param $shortname
     * @param SectionRepository $sectionR
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/classe/{shortname}", name="section")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')")
     *
     */
    public function section($shortname, SectionRepository $sectionR)
    {
        $page = 'section';
        $section = $sectionR->findOneBy(['shortname'=>$shortname]);

        return $this->render('page_section.html.twig', [
            'page' => $page,
            'section' => $section
        ]);
    }
}
