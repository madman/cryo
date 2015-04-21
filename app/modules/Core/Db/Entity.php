<?php

namespace Core\Db;

/**
 * Basic class for all entity
 */
abstract class Entity
{
    protected $id;

    public function __construct($data = [])
    {
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function isNew()
    {
        return is_null($this->getId());
    }

    abstract public function extract();
    abstract public function hydrate($data);

    public function __isset($name)
    {
        $method = 'get' . ucwords($name);

        if (method_exists($this, $method)) {
            return true;
        } elseif (property_exists($this, $name)) {
            return true;
        }

        return false;
    }

    public function __get($name)
    {
        $method = 'get' . ucwords($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . ucwords($name);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        } elseif (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
}
