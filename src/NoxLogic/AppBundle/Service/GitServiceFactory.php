<?php

namespace NoxLogic\AppBundle\Service;

use GitStash\Git;
use NoxLogic\AppBundle\Entity\Repository;


/**
 * Factory that allows us to instantiate gitService services
 *
 * Usage:
 *   $this->get('git_service_factory')->create(repository);
 */
class GitServiceFactory {

    /** @var Git */
    protected $git;

    /**
     * @param $basePath
     */
    function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Instantiates a new git service. Assumes that repository name and username are all lowercase.
     *
     * @param Repository $repo
     * @return GitService
     */
    function create(Repository $repo) {
        $path = $this->basePath.'/'.strtolower($repo->getOwner()->getUsernameCanonical())."/".strtolower($repo->getName()).".git";

        if (! is_dir($path)) {
            throw new \InvalidArgumentException('Repository path not found!');
        }

        $git = new Git($path);
        return new GitService($git);
    }

}
