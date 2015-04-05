<?php

namespace Core\Db;

class Collection implements \ArrayAccess
{
    protected $items = [];

    public function &getItems()
    {
        return $this->items;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
