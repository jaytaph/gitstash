<?php

namespace GitStash;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use GitStash\Git\Blob;
use GitStash\Git\Commit;
use GitStash\Git\Tag;
use GitStash\Git\Tree;
use GitStash\Git\TreeItem;

class Git {

    protected $path;
    protected $process;
    protected $pipes;

    function __construct($path)
    {
        $this->path = $path;

        $this->proc = null;
    }

    function __destruct()
    {
        $this->processClose();
    }

    protected function processOpen()
    {
        if ($this->process) {
            return;
        }

        $descriptors = array(
           0 => array("pipe", "r"),
           1 => array("pipe", "w"),
        );

        $this->process = proc_open("git --git-dir=".$this->path." cat-file --batch", $descriptors, $this->pipes);
    }

    protected function processClose()
    {
        if (! $this->process) {
            return;
        }

        fclose($this->pipes[0]);
        fclose($this->pipes[1]);

        proc_close($this->process);
    }

    /**
     * @param $sha
     * @return Object
     */
    function fetchObject($sha)
    {
        list($info, $content) = $this->fetchRawShaData($sha);

        return $this->createObject($info, $content);
    }

    protected function fetchRawShaData($sha) {

        $this->processOpen();

        // Write sha to git-cat-file
        fwrite($this->pipes[0], "$sha\n");

        // Read info back
        do {
            $info = trim(fgets($this->pipes[1]));
        } while (! strlen($info));

        // Read sha, type, size
        $info = array_combine(array('sha', 'type', 'size'), explode(" ", $info));
        $content = fread($this->pipes[1], $info['size']);

        return array($info, $content);
    }

    protected function createObject(array $info, $content)
    {
        switch ($info['type']) {
            case 'blob' :
                return $this->parseBlob($info, $content);
                break;
            case 'commit' :
                return $this->parseCommit($info, $content);
                break;
            case 'tag' :
                return $this->parseTag($info, $content);
                break;
            case 'tree' :
                return $this->parseTree($info, $content);
                break;
        }

        throw new \RuntimeException(sprintf("Cannot create object: Invalid type '%s'", $info['type']));
    }

    protected function parseBlob(array $info, $content)
    {
        return new Blob($info['sha'], $content);
    }

    protected function parseTree(array $info, $content)
    {
        preg_match_all('/([0-7]+) ([^\x00]+)\x00(.{20})/sm', $content, $matches);

        $tree = array();
        foreach (array_keys($matches[0]) as $k) {
            $tree[] = array(
                'perm' => $matches[1][$k],
                'name' => $matches[2][$k],
                'sha' => bin2hex($matches[3][$k]),
            );
        }

        return new Tree($info['sha'], $tree);
    }

    protected function parseCommit(array $info, $content)
    {
        // Parse headers until first empty \n
        $commit = array(
            'tree' => null,
            'parent' => null,
            'committer' => null,
            'author' => null,
            'log' => null,
            'log_details' => null,
        );
        do {
            list($line, $content) = explode("\n", $content, 2);

            if (strlen($line) == 0) break;

            list($type, $type_info) = explode(" ", $line, 2);
            $commit[$type] = $type_info;
        } while (strlen($line));

        // Parse commit log line and details (remainder lines)
        $content = explode("\n", $content, 2);
        $commit['log'] = $content[0];
        $commit['log_details'] = isset($content[1]) ? $content[1] : "";

        return new Commit(
            $info['sha'],
            $commit['tree'],
            $commit['parent'],
            $commit['committer'],
            $commit['author'],
            $commit['log'],
            $commit['log_details']
        );
    }

    protected function parseTag(array $info, $content)
    {
        // Parse headers until first empty \n
        $commit = array(
            'object' => null,
            'type' => null,
            'tag' => null,
            'tagger' => null,
        );
        do {
            list($line, $content) = explode("\n", $content, 2);

            if (strlen($line) == 0) break;

            list($type, $type_info) = explode(" ", $line, 2);
            $commit[$type] = $type_info;
        } while (strlen($line));

        // Parse commit log line and details (remainder lines)
        $content = explode("\n", $content, 2);
        $commit['log'] = $content[0];
        $commit['log_details'] = isset($content[1]) ? $content[1] : "";

        return new Tag(
            $info['sha'],
            $commit['object'],
            $commit['type'],
            $commit['tag'],
            $commit['tagger'],
            $commit['log'],
            $commit['log_details']
        );
    }

    function getRefs($type)
    {
        // Add file system refs
        $it = new \FilesystemIterator($this->path."/refs/".$type, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_PATHNAME);
        $ret = array();
        foreach ($it as $path) {
            $ret[basename($path)] = trim(file_get_contents($path));
        }

        // Add packed refs
        $path = $this->path . "/packed-refs";
        if (is_readable($path)) {
            $refs = file($path);
            foreach ($refs as $line) {
                $line = trim($line);

                // Comment
                if ($line[0] == '#') continue;

                // Annotated tag. Information is found in the previous line as well, so we don't do anything with it
                if ($line[0] == '^') continue;

                list($sha, $ref) = explode(" ", $line, 2);

                $a = explode('/', $ref);
                $refType = $a[1];
                $a = array_splice($a, 2);
                $ref = join('/', $a);
                ;
                if ($refType == $type) {
                    $ret[$ref] = $sha;
                }
            }
        }

        // Sort refs
        ksort($ret);

        return $ret;
    }

    /**
     * Returns the sha the reference points to
     *
     * @param $ref
     * @param string $base
     * @return string
     */
    function refToSha($ref, $base = 'heads') {
        $refs = $this->getRefs('heads');
        if (isset($refs[$ref])) {
            return $refs[$ref];
        }

        $refs = $this->getRefs('tags');
        if (isset($refs[$ref])) {
            return $refs[$ref];
        }

        throw new InvalidArgumentException(sprintf("Reference %s' not found", $ref));
    }

    /**
     * Returns a ref that has been packed (ie: located not in the /refs directory, but in the /packed-refs file)
     *
     * @param $wantedRef
     * @return mixed
     */
    protected function findPackedRef($wantedRef) {
        $refs = file($this->path . "/packed-refs");

        foreach ($refs as $line) {
            $line = trim($line);
            if ($line[0] == '#') continue;
            list($sha, $ref) = explode(" ", $line, 2);
            if ($ref == $wantedRef) {
                return $sha;
            }
        }

        throw new InvalidArgumentException('Ref $wantedRef not found in packed refs');
    }

}
