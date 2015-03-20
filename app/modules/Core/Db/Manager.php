<?php

namespace Core\Db;

abstract class Manager
{
    protected $app;
    protected $entity;
    protected $pk = 'id';

    /**
     * Default databse to connect
     */
    protected $connection = 'default';

    private $_mongo_collection;

    public function __construct(\Core\Application $app)
    {
        $this->app = $app;
    }

    public function getApp()
    {
        return $this->app;
    }

    /**
     * Return MongoCollection instance
     */
    public function getCollection()
    {
        if (null === $this->_mongo_collection) {
            $this->_mongo_collection = $this->app['mongo.' . $this->connection]->{$this->collection};
        }

        return $this->_mongo_collection;
    }

    /**
     * Returns collection name as string
     */
    public function getCollectionName()
    {
        return $this->collection;
    }

    public function getEntityClass()
    {
        if (null === $this->entity) {
            return str_replace('Manager', '', get_called_class());
        }

        return $this->entity;
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->toEntity($this->getCollection()->findOne($query, $fields));
    }

    public function find($query = [], $fields = [])
    {
        return new Collection($this, $query, $fields);
    }

    /**
     * Convert array to entity class.
     */
    public function toEntity($data)
    {
        if ($data) {
            $c               = $this->getEntityClass();
            $data[$this->pk] = (string)$data['_id'];

            return new $c($data);
        }
    }

    public function getPk()
    {
        return $this->pk;
    }

    /**
     * TODO: Move finders to traits
     */
    public function findById($id)
    {
        return $this->findOne(['_id' => new \MongoId($id)]);
    }

    /**
     * Proxy all undefined methods to MongoCollection
     */
    public function __call($method, $args)
    {
        if (method_exists($this->getCollection(), $method)) {
            $result = call_user_func_array([$this->getCollection(), $method], $args);

            return $result;
        }

        throw new \Exception("Method $method does not exists in " . get_class($this));
    }
}
