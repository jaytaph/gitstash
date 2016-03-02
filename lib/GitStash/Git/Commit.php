<?php

namespace GitStash\Git;

final class Commit implements Object {

    protected $sha;
    protected $tree;
    protected $parent;
    protected $committer;
    protected $author;
    protected $log;
    protected $detailed_log;
    protected $date;

    function __construct($sha, $tree, $parent, $committer, $author, $log, $detailed_log)
    {
        $this->sha = $sha;
        $this->tree = $tree;
        $this->parent = $parent;
        preg_match('|(.+) <(.+)> (\d+) ([-+]\d\d\d\d)|', $committer, $match);

        $dt = new \DateTime("@".$match[3], new \DateTimeZone($match[4]));
        $this->date = $dt;
        $this->committer = $committer;
        $this->author = $author;
        $this->log = $log;
        $this->detailed_log = $detailed_log;
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
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return mixed
     */
    public function getCommitter()
    {
        return $this->committer;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return mixed
     */
    public function getDetailedLog()
    {
        return $this->detailed_log;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

}
