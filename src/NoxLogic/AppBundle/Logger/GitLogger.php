<?php

namespace NoxLogic\AppBundle\Logger;

class GitLogger {

    protected $calls = array('cache' => array(), 'nocache' => array());

    function getCalls()
    {
        return $this->calls;
    }

    function addCall($sha, $type, $cached = false) {
        $ck = $cached ? "cache" : "nocache";

        if (! isset($this->calls[$ck][$sha])) {
            $this->calls[$ck][$sha] = array(
                'count' => 0,
                'type' => $type,
                'sha' => $sha,
            );
        }

        $this->calls[$ck][$sha]['count']++;
    }
}
