<?php

namespace Core\Db;

use Core\ApplicationRegistry;
use Core\Reflection;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Basic class for all mongo documents.
 */
abstract class Entity
{
    /** @Field */
    protected $_id;

    /**
     * This is alias for $_id property.
     */
    protected $id;

    /**
     * @var array of errors after isValid called.
     */
    protected $errors;

    public function __construct($properties = [])
    {
        $this->populateProperties($properties);
    }

    /**
     * Sure, its not best solution.
     * You should use this method not often.
     */
    public function getApp()
    {
        return ApplicationRegistry::get();
    }

    /**
     * @param $tag
     * @param $class
     * @param $value
     * @return array
     */
    protected function populateModelProperty($value, $class, $tag = 'Model')
    {
        if (!empty($value)) {
            if ($tag == 'MultipleModel') {
                if (!empty($value) && is_array($value)) {
                    array_walk($value, function (&$item) use ($class) {
                        $item = $this->populateModelProperty($item, $class);
                    });
                }
            } else {
                $value = new $class($value);
                $value->setId((string)$value->getMongoId());
            }
        }

        return $value;
    }

    /**
     * Convert fields to Entity if need
     * @param $value
     * @param $tags
     * @return mixed
     * @throws Exception
     */
    protected function populateModelProperties($value, $tags)
    {
        foreach ($tags as $tag_name => $contexts) {
            if (count($contexts) > 1) {
                throw new Exception("Multiple models options for fields prohibited!");
            }
            $contexts = reset($contexts);

            $parts = preg_split('/\s+/', $contexts);
            $class = reset($parts);
            if (!class_exists($class)) {
                throw new Exception("Field class does not found!");
            }
            if (!is_subclass_of($class, '\Core\Db\Entity')) {
                throw new Exception("Field class must be instance of Entity!");
            }

            $value = $this->populateModelProperty($value, $class, $tag_name);
        }

        return $value;
    }

    /**
     * populate records
     * @param $properties
     */
    protected function populateProperties($properties)
    {
        if (!is_array($properties)) return;
        $modelProperties = (new Reflection($this))->getPropertiesByTag(['@Model', '@MultipleModel']);

        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                if (in_array($key, $modelProperties)) {
                    $tags = (new Reflection($this))->getPropertyTags($key, ['Model', 'MultipleModel']);
                    $value = $this->populateModelProperties($value, $tags);
                }
                $this->$key = $value;
            }
        }
    }

    /**
     * @return Manager
     */
    public static function manager()
    {
        $managerName = get_called_class() . 'Manager';

        return new $managerName(ApplicationRegistry::get());
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMongoId()
    {
        return $this->_id;
    }

    /**
     * @param $item
     * @return array
     */
    protected function saveChildModelsField($item)
    {
        if (!empty($item)) {
            if ($item instanceof Entity) {
                $item = $item->save($this);
            } elseif (is_array($item)) {
                array_walk($item, function (&$item) {
                    $item = $this->saveChildModelsField($item);
                });
            }
        }

        return $item;
    }

    /**
     * Get all values with tags MultipleModel and Model
     * and save it recursive
     */
    protected function saveChildModels()
    {
        $reflection = new Reflection($this);
        $multipleModel = $reflection->getPropertiesByTag('@MultipleModel');
        foreach ($multipleModel as $field) {
            $value = $this->saveChildModelsField($this->__get($field));
            $this->__set($field, is_array($value) ? array_values($value) : $value);
        }

        $model = $reflection->getPropertiesByTag('@Model');
        foreach ($model as $field) {
            $value = $this->saveChildModelsField($this->__get($field));
            $this->__set($field, $value->save($this));
        }
    }

    /**
     * If this main model, then save to DB and return update/insert result
     * If this is child model, don't save to DB, manually add _id and return converted to array values
     *
     * @param Entity $parent
     * @return array
     */
    public function save($parent = null)
    {
        $this->beforeSave();
        $this->saveChildModels();

        $arr = $this->toArray();
        if (!empty($parent)) {
            $arr['_id'] = ($arr['_id'] === null) ? new \MongoId() : $arr['_id'];
            $result = $arr;
        } else {
            // Remove _id to make MongoCollection availabe to update it.
            if ($arr['_id'] === null) {
                unset($arr['_id']);
            }
            //save to db
            $result = $this::manager()->save($arr);
        }
        $this->populateProperties($arr);

        if ($arr['_id']) {
            $this->setId((string)$arr['_id']);
        }

        $this->afterSave();
        return $result;
    }

    public function remove()
    {
        if ($this->isNew()) {
            return;
        }

        $this->beforeRemove();

        $result = $this::manager()->remove(['_id' => new \MongoId($this->getId())], ['justOne' => true]);

        $this->afterRemove();

        return $result;
    }

    public function isNew()
    {
        return $this->getId() === null;
    }

    /**
     * Returns array of document fields.
     */
    public function getFields()
    {
        return (new Reflection($this))->getPropertiesByTag('@Field');
    }

    public function toArray()
    {
        $result = [];

        foreach ($this->getFields() as $key) {
            $result[$key] = $this->$key;
        }

        return $result;
    }

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

    public function isValid()
    {
        $this->errors = $this->app['validator']->validate($this);

        return $this->errors->count() === 0;
    }

    /**
     * Returns validation result. Call isValid before manually.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function __call($name, $arguments)
    {
        $parsed_name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));

        if (strpos($name, 'get') === 0) {
            return $this->{str_replace('get_', '', $parsed_name)};
        }

        if (strpos($name, 'set') === 0) {
            $parsed_name        = str_replace('set_', '', $parsed_name);
            $this->$parsed_name = $arguments[0];

            return;
        }

        throw new \Exception('Method "' . $name . '" not found');
    }

    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }

    public function beforeRemove()
    {
    }

    public function afterRemove()
    {
    }

}
