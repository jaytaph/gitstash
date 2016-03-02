<?php

namespace GitStash\Git;

final class Blob implements Object {

    protected $sha;
    protected $contents;

    function __construct($sha, $contents)
    {
        $this->sha = $sha;
        $this->contents = $contents;
    }

    /**
     * @return mixed
     */
    public function getSha()
    {
        return $this->sha;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

}
