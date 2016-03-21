<?php

namespace NoxLogic\AppBundle\Controller;

use NoxLogic\AppBundle\Entity\AuthorizedKeys;
use NoxLogic\AppBundle\Entity\Repository;
use NoxLogic\AppBundle\Entity\User;
use NoxLogic\AppBundle\Form\Type\RepoFormType;
use NoxLogic\AppBundle\Form\Type\SshKeyFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

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

        $form = $this->createForm(SshKeyFormType::class);

        return $this->render('NoxLogicAppBundle:User:settings.html.twig', array(
            'user' => $user,
            'sshkeyForm' => $form->createView(),
        ));
    }

    public function sshkeyAjaxAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(SshKeyFormType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $sshKey = new AuthorizedKeys();
            $sshKey->setSshkey($form->get('key')->getData());
            $sshKey->setLabel($form->get('label')->getData());
            $sshKey->setUser($user);

            $pubKeyService = new \GitStash\Ssh\PubKey($form->get('key')->getData());
            $sshKey->setFingerprint($pubKeyService->getFingerprint());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($sshKey);
            $manager->flush();

            // Add new key
            return new JsonResponse(array(), 200);
        }

        return new JsonResponse(array('error' => 'incorrect data'), 400);
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
        $form = $this->createform(RepoFormType::class, $repo);

        $form->handleRequest($request);
        if ($form->isValid()) {
            // Create repository
            $repositoryService = $this->get('repo_service');
            $result = $repositoryService->create($user, $repo);

            if ($result) {
                return $this->redirect($this->generateUrl('repo', array('user' => $user->getUsername(), 'repo' => $repo->getName())));
            }
        }

        return $this->render('NoxLogicAppBundle:User:newRepository.html.twig', array(
            'user' => $user,
            'repo_form' => $form->createView()
        ));
    }

}
