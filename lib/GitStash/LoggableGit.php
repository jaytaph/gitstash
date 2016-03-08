<?php

namespace GitStash;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use GitStash\Git\Blob;
use GitStash\Git\Commit;
use GitStash\Git\Tree;
use GitStash\Git\TreeItem;
use NoxLogic\AppBundle\Logger\GitLogger;

class LoggableGit extends Git {

    /** @var GitLogger */
    protected $logger;

    function setLogger(GitLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $sha
     * @return Object
     */
    function fetchObject($sha)
    {
        $object = parent::fetchObject($sha);
        $this->logger->addCall($sha, get_class($object));

        return $object;
    }

}
