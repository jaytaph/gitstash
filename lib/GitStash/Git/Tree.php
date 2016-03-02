<?php

namespace GitStash\Git;

final class Tree implements Object, \IteratorAggregate, \ArrayAccess {

    function __construct($sha, array $items)
    {
        $this->sha = $sha;

        foreach ($items as $item) {
            $this->items[$item['name']] = new TreeItem($item['name'], $item['sha'], $item['perm']);
        }
    }

    /**
     * @return mixed
     */
    public function getSha()
    {
        return $this->sha;
    }

    public function getItems() {
        return new \ArrayIterator($this->items);
    }

    public function getIterator()
    {
        return $this->getItems();
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        // Cannot set
    }

    public function offsetUnset($offset)
    {
        // Cannot unset
    }


}
