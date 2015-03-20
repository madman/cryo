<?php

namespace Core\Db;

use EBT\Collection\CollectionDirectAccessInterface;
use EBT\Collection\CountableTrait;
use EBT\Collection\DirectAccessTrait;
use EBT\Collection\EmptyTrait;
use EBT\Collection\GetItemsTrait;
use EBT\Collection\IterableTrait;

class Collection implements CollectionDirectAccessInterface, \ArrayAccess
{
    use CountableTrait;
    use DirectAccessTrait;
    use EmptyTrait;
    use GetItemsTrait;
    use IterableTrait;

    protected $items = [];
    protected $isItemsLoaded = false;

    public function __construct($manager, $query = [], $fields = [])
    {
        $this->manager = $manager;
        $this->query   = $query;
        $this->fields  = $fields;
        $this->cursor  = $this->manager->getCollection()->find($this->query, $this->fields);
    }

    public function count()
    {
        return $this->cursor->count();
    }

    public function &getItems()
    {
        $this->loadItems();

        return $this->items;
    }

    protected function loadItems()
    {
        if (!$this->isItemsLoaded) {
            $this->items         = $this->toEntities($this->cursor);
            $this->isItemsLoaded = true;
        }

        return $this->items;
    }

    public function toEntities($records)
    {
        $result = [];

        foreach ($records as $r) {
            $class                      = $this->manager->getEntityClass();
            $r[$this->manager->getPk()] = (string)$r['_id'];

            $result[] = new $class($r);
        }

        return $result;
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

    /**
     * Performs action on each collection element
     */
    public function each($callable)
    {
        foreach ($this->getItems() as $item) {
            $callable($item);
        }
    }

    /**
     * Remove each entity from items
     */
    public function removeAll()
    {
        foreach ($this->getItems() as $key => $item) {
            $item->remove();
            unset($this[$key]);
        }
    }

    /**
     * By default `getItems` return integer indexed array, e.g.:
     * [0=>...,1=>...]
     * But, when using mongo sometimes we need keys as real mongo id.
     * This method will return next result:
     * ['entity_id'=>...,'sdfsdf3d23dsdf32'=>...]
     */
    public function getItemsWithKeyAsId()
    {
        $result = [];

        foreach ($this->getItems() as $item) {
            $result[$item->getId()] = $item;
        }

        return $result;
    }

    /**
     * Proxy all undefined methods to MongoCursor
     */
    public function __call($method, $args)
    {
        if (method_exists($this->cursor, $method)) {
            $result = call_user_func_array([$this->cursor, $method], $args);

            return $this;
        }

        throw new \Exception("Method $method does not exists in " . get_class($this->cursor));
    }
}
