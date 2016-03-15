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
        $vars = $this->getRepoVars($repo, $tree, $path);

        return $this->render('NoxLogicAppBundle:Repo:tree.html.twig', $vars);
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
    public function blobAction(Request $request, Repository $repo, $tree, $path)
    {
        $file = basename($path);
        $path = dirname($path);

        $vars = $this->getRepoVars($repo, $tree, $path);
        $vars['file'] = $file;

        return $this->render('NoxLogicAppBundle:Repo:blob.html.twig', $vars);
    }

    protected function getRepoVars(Repository $repo, $tree, $path)
    {
        $gitService = $this->get('git_service_factory')->create($repo);

        $branch = $tree; // Or this could be a tag
        $commit = $gitService->fetchCommitFromRef($tree);
        $tree = $gitService->getTreeFromBranchPath($tree, $path);


        $crumbtrail = array();

        if (strlen($path) > 0 && $path[0] == "/") {
            $path = substr($path, 1);
        }
        if ($path == "") {
            $pathArray = array("");
        } else {
            $pathArray = explode("/", $path);
        }
        array_unshift($pathArray, "");

        $p = array();
        foreach ($pathArray as $element) {
            $p[] = $element;
            $thisPath = join('/', $p);
            if ($element == "") {
                $element = "Root";
            }
            $crumbtrail[] = array(
                'name' => $element,
                'href' => $this->generateUrl('repo_tree', array(
                    'user' => $repo->getOwner()->getUsername(),
                    'repo' => $repo->getName(),
                    'tree' => $branch,
                    'path' => $thisPath,
                )),
            );
        }

        return array(
            'repo' => $repo,
            'git' => $gitService,
            'tree' => $tree,
            'commit' => $commit,
            'branch' => $branch,
            'crumbtrail' => $crumbtrail,
            'path' => $path,
        );
    }

}
