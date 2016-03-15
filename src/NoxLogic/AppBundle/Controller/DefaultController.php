<?php

namespace NoxLogic\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * Display main page
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $repos = $this->getDoctrine()->getRepository('NoxLogicAppBundle:Repository')->findRandomRepositories(10);

        return $this->render('NoxLogicAppBundle:Default:index.html.twig', array(
            'repos' => $repos,
        ));
    }
}
