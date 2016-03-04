<?php

namespace NoxLogic\AppBundle\Controller;

use NoxLogic\AppBundle\Entity\Repository;
use NoxLogic\AppBundle\Entity\User;
use NoxLogic\AppBundle\Form\Type\RepoFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * Displays main user page
     *
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("user", options={ "mapping" : { "user" : "username" } })
     */
    public function indexAction(Request $request, User $user)
    {
        return $this->render('NoxLogicAppBundle:User:index.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Displays settings page for logged in user
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {
        $user = $this->getUser();

        return $this->render('NoxLogicAppBundle:User:settings.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Display new repository page for user.
     *
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("user", options={ "mapping" : { "user" : "username" } })
     */
    public function newRepositoryAction(Request $request, User $user)
    {
        $repo = new Repository();
        $form = $this->createform(new RepoFormType(), $repo);

        $form->handleRequest($request);
        if ($form->isValid()) {
            // Create repository
            $repositoryService = $this->get('repo_service');
            $result = $repositoryService->create($user, $repo);

            if ($result) {
                $this->redirect($this->generateUrl('user', array('user' => $user)));
            }
        }

        return $this->render('NoxLogicAppBundle:User:newRepository.html.twig', array(
            'user' => $user,
            'repo_form' => $form->createView()
        ));
    }

}
