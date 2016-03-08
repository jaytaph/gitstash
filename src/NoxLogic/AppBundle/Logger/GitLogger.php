<?php

namespace NoxLogic\AppBundle\Logger;

class GitLogger {

    protected $calls = array();

    function getCalls()
    {
        return $this->calls;
    }

    function addCall($sha, $type) {
        if (! isset($this->calls[$sha])) {
            $this->calls[$sha] = array(
                'count' => 0,
                'type' => $type,
                'sha' => $sha,
            );
        }

        $this->calls[$sha]['count']++;
    }
}
