<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/shows/{page}", requirements={"page" = "\d+"}, options={"expose"=true}, name="shows")
     * @Template()
     * 
     * @param $page Le numéro de la page souhaité
     */
    public function showsAction($page = 1)
    {
        $showsNumberByPage = 6;
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');

        $shows = $repo->findAllWithPagination($page, $showsNumberByPage);
        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($shows) / $showsNumberByPage),
            'nomRoute' => 'shows',
            'paramsRoute' => []
        ];

        return [
            'shows' => $shows,
            'pagination' => $pagination
        ];
    }

    /**
     * @Route("/show/{id}", name="show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');

        return [
            'show' => $repo->find($id)
        ];        
    }

    /**
     * @Route("/calendar", name="calendar")
     * @Template()
     *
     * @param $page Le numéro de la page souhaité
     */
    public function calendarAction()
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Episode');

        $episodes = $repo->findNextEpisodesOrderByDate();

        return [
            'episodes' => $episodes,
        ];
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        return [];
    }

    /**
     * @Route("/search/{page}", requirements={"page" = "\d+"}, name="search_shows")
     * @param $page Le numéro de la page souhaité
     */
    public function searchAction(Request $request, $page = 1)
    {
        $showsNumberByPage = 6;
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:TVShow');

        $keyword = $request->request->get("keyword");

        if (null == $keyword) {
            $keyword = $this->get('session')->get('keyword');
        } else {
            $this->get('session')->set('keyword', $keyword);
        }
        
        if($keyword) {
            $shows = $repo->searchWithPagination($keyword, $page, $showsNumberByPage);
        } else {
            $shows = $repo->findAllWithPagination($page, $showsNumberByPage);
        }

        $pagination = [
            'page' => $page,
            'nbPages' => ceil(count($shows) / $showsNumberByPage),
            'nomRoute' => 'search_shows',
            'paramsRoute' => []
        ];

        return $this->render('AppBundle:Default:shows.html.twig', [
            'shows' => $shows,
            'keyword' => $keyword,
            'pagination' => $pagination
        ]);
    }
}
