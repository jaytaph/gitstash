<?php

namespace GitStash\Git;

final class Tag implements Object {

    protected $sha;
    protected $object;
    protected $type;
    protected $tag;
    protected $tagger;
    protected $log;
    protected $detailed_log;
    protected $date;

    function __construct($sha, $object, $type, $tag, $tagger, $log, $detailed_log)
    {
        $this->sha = $sha;
        $this->object = $object;
        $this->type = $type;
        $this->tag = $tag;
        preg_match('|(.+) <(.+)> (\d+) ([-+]\d\d\d\d)|', $tagger, $match);

        $dt = new \DateTime("@".$match[3], new \DateTimeZone($match[4]));
        $this->date = $dt;
        $this->tagger = $tagger;

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
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return mixed
     */
    public function getTagger()
    {
        return $this->tagger;
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
