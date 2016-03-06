<?php

namespace NoxLogic\AppBundle\Service;

use GitStash\Git;
use GitStash\Git\Blob;
use GitStash\Git\Commit;
use GitStash\Git\Tree;
use GitStash\Git\TreeItem;

/**
 * Class GitService
 *
 * Git service that allows us to easily communicate with git. Mostly used directly into Twig, but can be used for other
 * purposes as well.
 */
class GitService {

    /** @var Git */
    protected $git;

    /**
     * @param Git $git
     */
    function __construct(Git $git) {
        $this->git = $git;

    }

    /**
     * Returns a list of all "branches"
     *
     * Returned as array(<branch> => <sha>, ..)
     *
     * @return string[]
     */
    function getBranches() {
        return $this->git->getRefs("heads");
    }

    /**
     * Returns a list of all tags
     *
     * Returned as array(<tagname> => <sha>, ..)
     *
     * @return string[]
     */
    function getTags() {
        return $this->git->getRefs("tags");
    }

    /**
     * Converts a reference to a sha commit (ie:  "master" to 03522a..)
     *
     * Note: does not work with tags
     *
     * @param string $ref
     * @return string sha
     */
    function refToSha($ref) {
        return $this->git->refToSha($ref);
    }

    function fetchObject($sha)
    {
        return $this->git->fetchObject($sha);
    }

    /**
     * Get a blob from a given file from a given tree sha
     *
     * @param $sha
     * @param $file
     * @return Blob
     */
    function getContentFromTree($sha, $file)
    {
        // Fetch the tree item from the given file from the given sha
        $treeItem = $this->getFromTree($sha, $file);

        // Return blob from the tree item sha
        return $this->git->fetchObject($treeItem->getSha());
    }

    /**
     * Return a treeItem for a given file from a given sha
     *
     * @param $sha
     * @param $file
     * @return TreeItem
     */
    function getFromTree($sha, $file) {
        // Find the tree from the given sha
        $tree = $this->getTreeFromSha($sha);

        // Return
        if (isset($tree[$file])) {
            return $tree[$file];
        }

        throw new \InvalidArgumentException(sprintf("'%s' not found in tree commit %s", $file, $tree->getSha()));
    }

    /**
     * Return tree from given sha. If the sha is a commit, it will fetch the tree from that commit
     *
     * @param $sha
     * @return Tree
     */
    function getTreeFromSha($sha) {
        $tree = $this->git->fetchObject($sha);
        if ($tree instanceof Commit) {
            $tree = $this->git->fetchObject($tree->getTree());
        }

        if (! $tree instanceof Tree) {
            throw new \InvalidArgumentException(sprintf("Sha '%s' is not a tree (or commit)", $sha));
        }

        return $tree;
    }

    /**
     * Checks if file is present in given sha
     *
     * @param $sha
     * @param $file
     * @return bool
     */
    function existsInTree($sha, $file) {
        try {
            $tree = $this->getTreeFromSha($sha);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return (isset($tree[$file]));
    }

    /**
     * Based on a branch and path, get its tree
     *
     * This will return the tree based on the base branch (ie: master) and path ("/src/foo/bar.php")
     *
     * @param $branch
     * @param $path
     * @return Tree
     */
    function getTreeFromBranchPath($branch, $path)
    {
        // Convert branch to sha
        $sha = $this->refToSha($branch);

        // Proceed with fetching from sha
        return $this->getTreeFromShaPath($sha, $path);
    }


    /**
     * This will return the tree based on the SHA and path ("/src/foo/bar.php")
     *
     * @param $sha
     * @param $path
     * @return Tree
     */
    function getTreeFromShaPath($sha, $path)
    {
        // Get tree from sha
        $tree = $this->getTreeFromSha($sha);

        // Iterate path elements and
        $path = array_filter(explode("/", $path));
        while (count($path)) {
            $dir = array_shift($path);

            if (isset($tree[$dir]) && $tree[$dir]->isDir()) {
                $sha = $tree[$dir]->getSha();
                $tree = $this->getTreeFromSha($sha);
            }
        }

        return $tree;
    }


    /**
     * Get tree "information" based on a sha and path. This information consist of the following:
     *
     *    item        TreeItem object for the given file
     *    name        Name of the given file
     *    sha         Sha of the given file blob
     *    commit_sha  Sha of the LAST commit that changed this file
     *    commit_log  Log line of the LAST commit that changed this file
     *    commit_date Date of the LAST commit (from the committer, not the author)
     *
     * This is a difficult setup, as we need to parse all parents (@TODO: what about multiple parents?), and we need to
     * parse trees, when the file is not in the root of the commit.
     *
     * Furthermore, we must keep in track of each sha for each file to detect if the given commit has changed the file
     * or not. Unfortunately, it seems that git doesn't store this information very efficiently, so it might be wise
     * to come up with a more performant system, and maybe slap on some caching here and there as well.
     *
     * @param $sha
     * @return array
     */
    function getTreeInfo(Commit $commit, $path = "/")
    {
        // Fetch tree for given path in the initial SHA
        $mainTree = $this->getTreeFromShaPath($commit->getSha(), $path);

        // These are all the files that needs to be checked.
        $todo = array();
        foreach ($mainTree->getItems() as $item) {
            // All files are stored in the to do list
            $todo[] = $item->getName();

            // Set initial information, we change commit_* values on each iteration if needed
            $files[$item->getName()] = array(
                'item' => $item,
                'name' => $item->getName(),
                'sha' => $item->getSha(),
                'commit_sha' => $commit->getSha(),
                'commit_log' => $commit->getLog(),
                'commit_date' => $commit->getDate(),
                'cnt' => 0,
            );

        }

        // Iterate until we run out of commits
        while ($commit) {
            // Fetch tree for given commit
            $rootTree = $this->git->fetchObject($commit->getTree());
            $tree = $this->getTreeFromShaPath($rootTree->getSha(), $path);

//            // First, we need to browse the tree to the correct spot based on path
//            $pathArray = array_filter(explode("/", trim($path, '/')));
//            while (count($pathArray)) {
//                $dir = array_shift($pathArray);
//                $tree = isset($tree[$dir]) ? $this->git->fetchObject($tree[$dir]->getSha()) : array();
//            }

            // Iterate all items that are left to do
            foreach ($todo as $k => $item) {

                // Item does not exist at this point in time, so remove from list, and continue with next
                if (! isset($tree[$item])) {
                    $todo[$k] = null;
                    continue;
                }

                // Sha is different, this is the moment the file has last changed so remove from our to do list
                if ($tree[$item]->getSha() != $files[$item]['sha']) {
                    $todo[$k] = null;
                    continue;
                }

                // File is the same in this commit as in the last, so we "change" the commit message to the current one.
                // This way, the commit information will trickle down until we hit one of the checks above that remove
                // the file from our to do list.
                $files[$item]['sha'] = $tree[$item]->getSha();
                $files[$item]['commit_sha'] = $commit->getSha();
                $files[$item]['commit_log'] = $commit->getLog();
                $files[$item]['commit_date'] = $commit->getDate();
                $files[$item]['cnt']++;
            }

            // Remove all null values from our to do list
            $todo = array_filter($todo);

            // Drop down to the parent commit, if any
            $commit = $commit->getParent() ? $this->git->fetchObject($commit->getParent()) : null;
        }

        // Sanity check, we should have no more files left in our to do list.
        if (count($todo) > 0) {
            throw new \RuntimeException('It seems that we ran out of parent commits, but we still have files in our todo');
        }

        // Sort files on directory first, and name next
        uasort($files, function ($a, $b) {
            // If file permissions are the same, we sort by name
            if ($a['item']->getPerm() == $b['item']->getPerm()) {
                return $a['name'] > $b['name'];
            }

            // We sort between files and directories by checking permissions. Crude but effective
            return $a['item']->getPerm() > $b['item']->getPerm();
        });

        // Finally, we can return the file info
        return $files;
    }

}
