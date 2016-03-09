<?php

namespace GitStash;

use NoxLogic\AppBundle\Logger\GitLogger;

class CachableGit extends Git {

    /** @var \Predis\Client */
    protected $redis;

    /** @var GitLogger */
    protected $logger;


    function setRedis(\Predis\Client $redis)
    {
        $this->redis = $redis;
    }

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
        $data = $this->redis->get($this->path . '/' . $sha);
        if ($data) {
            $cached = true;

            list($info, $content) = explode("\n", $data, 2);
            $info = array_combine(array('sha', 'type', 'size'), explode(" ", $info));

            $object = $this->createObject($info, $content);

        } else {
            $cached = false;

            list($info, $content) = $this->fetchRawShaData($sha);
            $object = $this->createObject($info, $content);

            $header = $info['sha']." ".$info['type']." ".$info['size']."\n";
            $this->redis->set($this->path . '/' . $sha, $header . $content);
        }

        $this->logger->addCall($sha, get_class($object), $cached);

        return $object;
    }

}
