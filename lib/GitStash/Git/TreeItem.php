<?php

namespace GitStash\Git;

final class TreeItem {

    const S_IFDIR = 040000;

    protected $sha;
    protected $name;
    protected $perm;

    function __construct($name, $sha, $perm)
    {
        $this->sha = $sha;
        $this->name = $name;
        $this->perm = intval($perm, 8); // Octal permissions
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPerm()
    {
        return $this->perm;
    }

    public function isDir()
    {
        return $this->perm & self::S_IFDIR;
    }

}
