<?php

namespace NoxLogic\AppBundle\Controller;

use NoxLogic\AppBundle\Entity\Repository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RepoController extends Controller
{

    /**
     * Displays main repository information (ie: master tree)
     *
     * @param Request $request
     * @param Repository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("repo", options={
     *    "repository_method" = "findByUserNameAndRepo",
     *    "mapping": {"user": "userName", "repo": "repoName"},
     *    "map_method_signature" = true
     * })
     */
    public function indexAction(Request $request, Repository $repo)
    {
        $gitService = $this->get('git_service_factory')->create($repo);

        return $this->render('NoxLogicAppBundle:Repo:index.html.twig', array(
            'repo' => $repo,
            'git' => $gitService,
        ));
    }

    /**
     * Display specific tree based on branch/ref.
     *
     * @param Request $request
     * @param Repository $repo
     * @param string $tree (ie: master)
     * @param string $path (ie: /)
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("repo", options={
     *    "repository_method" = "findByUserNameAndRepo",
     *    "mapping": {"user": "userName", "repo": "repoName"},
     *    "map_method_signature" = true
     * })
     */
    public function treeAction(Request $request, Repository $repo, $tree, $path)
    {
        $gitService = $this->get('git_service_factory')->create($repo);

        return $this->render('NoxLogicAppBundle:Repo:tree.html.twig', array(
            'repo' => $repo,
            'git' => $gitService,
            'tree' => $gitService->getTreeFromBranchPath($tree, $path),
            'commit' => $gitService->fetchCommitFromRef($tree),
            'branch' => $tree,
            'path' => explode("/", $path),
        ));
    }

    /**
     * Display given blob in given tree
     *
     * @param Request $request
     * @param Repository $repo
     * @param string $tree (ie: master)
     * @param string $path (ie: /)
     * @param string $file (ie: README.md)
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("repo", options={
     *    "repository_method" = "findByUserNameAndRepo",
     *    "mapping": {"user": "userName", "repo": "repoName"},
     *    "map_method_signature" = true
     * })
     */
    public function blobAction(Request $request, Repository $repo, $tree, $path, $file)
    {
        $gitService = $this->get('git_service_factory')->create($repo);

        return $this->render('NoxLogicAppBundle:Repo:blob.html.twig', array(
            'repo' => $repo,
            'git' => $gitService,
            'tree' => $gitService->getTreeFromBranchPath($tree, $path),
            'branch' => $tree,
            'path' => explode("/", $path),
            'file' => $file,
        ));
    }

}
