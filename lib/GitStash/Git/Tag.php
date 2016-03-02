<?php

namespace GitStash\Git;

final class Tag implements Object {

    function __construct($sha)
    {
        $this->sha = $sha;
    }

    /**
     * @return mixed
     */
    public function getSha()
    {
        return $this->sha;
    }

}
