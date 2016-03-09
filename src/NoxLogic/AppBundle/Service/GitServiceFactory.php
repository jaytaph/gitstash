<?php

namespace NoxLogic\AppBundle\Service;

use GitStash\CachableGit;
use GitStash\Git;
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

    /** @var \Predis\Client */
    protected $redis;

    /**
     * @param $basePath
     */
    function __construct($basePath, GitLogger $logger, \Predis\Client $redis)
    {
        $this->basePath = $basePath;
        $this->logger = $logger;
        $this->redis = $redis;
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

        $git = new CachableGit($path);
        $git->setRedis($this->redis);
        $git->setLogger($this->logger);

        return new GitService($git);
    }

}
