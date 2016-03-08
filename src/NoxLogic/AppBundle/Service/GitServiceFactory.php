<?php

namespace NoxLogic\AppBundle\Service;

use GitStash\Git;
use GitStash\LoggableGit;
use NoxLogic\AppBundle\Entity\Repository;
use NoxLogic\AppBundle\Logger\GitLogger;


/**
 * Factory that allows us to instantiate gitService services
 *
 * Usage:
 *   $this->get('git_service_factory')->create(repository);
 */
class GitServiceFactory {

    /** @var Git */
    protected $git;

    /** @var GitLogger */
    protected $logger;

    /**
     * @param $basePath
     */
    function __construct($basePath, GitLogger $logger)
    {
        $this->basePath = $basePath;
        $this->logger = $logger;
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

        $git = new LoggableGit($path);
        $git->setLogger($this->logger);

        return new GitService($git);
    }

}
