<?php


namespace Studiow\Spot;

use Spot\Entity as SpotEntity;
use Ramsey\Uuid\Uuid;
use Spot\EventEmitter;

abstract class Entity extends SpotEntity
{

    protected static $mapper = Mapper::class;

    public static function guid()
    {
        return (string)Uuid::uuid4();
    }

    public static function hasField($name)
    {
        return array_key_exists($name, static::fields());
    }

    public static function getFieldInfo($name)
    {
        $fields = static::fields();
        if (!array_key_exists($name, $fields)) {
            throw new \Exception("Field {$name} does not exist");
        }
        return $fields[$name];
    }

    public static function getFieldParam($name, $param, $default = null)
    {
        $info = static::getFieldInfo($name);
        return array_key_exists($param, $info) ? $info[$param] : $default;
    }


    public function set($field, $value, $modified = true)
    {
        if (self::hasField($field)) {
            $type = static::getFieldParam($field, 'type', 'text');

            $convertor = "to" . ucfirst($type);

            if (is_callable([$this, $convertor])) {
                $value = call_user_func_array([$this, $convertor], [$value, $field]);
            }

        }
        return parent::set($field, $value, $modified);
    }


    protected function toBoolean($value, $field)
    {
        return (bool)$value;
    }

    protected function toGuid($value, $field)
    {
        if (!preg_match('#^[0-9A-Za-z]{8}-[0-9A-Za-z]{4}-[0-9A-Za-z]{4}-[0-9A-Za-z]{4}-[0-9A-Za-z]{12}$#', $value)) {

            $value = null;
        }
        return $value;
    }

    protected function toDatetime($value, $field)
    {
        if (!is_a($value, '\DateTime')) {
            if (is_null($value)) {
                return null;
            }
            return new \DateTime($value);
        }
        return $value;
    }

    protected function toString($value, $field)
    {
        if (is_null($value)) {
            return null;
        }
        $parsers = self::getFieldParam($field, 'parse', []);
        array_map(function ($parser) use (&$value) {

            $value = $parser($value);
        }, $parsers);
        return trim($value);
    }

    protected function toText($value, $field)
    {
        return $this->toString($value, $field);
    }

    protected function toInteger($value, $field)
    {
        return intval($value);
    }

    protected function toBigint($value, $field)
    {
        return $this->toInteger($value, $field);
    }

    protected function toSmallint($value, $field)
    {
        return $this->toInteger($value, $field);
    }

    public static function events(EventEmitter $eventEmitter)
    {
        if (static::hasField('created_at')) {
            $eventEmitter->on(Event::BEFORE_INSERT, function ($entity, $mapper) {
                $ts = new \DateTime();
                if (empty($entity->created_at)) {
                    $entity->created_at = $ts;
                    if (static::hasField('updated_at') && empty($entity->updated_at)) {
                        $entity->updated_at = $ts;
                    }
                }
            });
        }

        if (static::hasField('updated_at')) {
            $eventEmitter->on(Event::BEFORE_SAVE, function ($entity, $mapper) {
                $entity->updated_at = new \DateTime();
            });
        }
        if (static::hasField('deleted')) {
            $eventEmitter->on(Event::BEFORE_DELETE, function ($entity, $mapper) {


                $entity->deleted = true;
                $mapper->save($entity);
                return false;

            }, 9999);


            $eventEmitter->on(Event::BEFORE_DELETE_CONDITIONS, function ($conditions, Mapper $mapper) {


                $items = $mapper->where($conditions);
                foreach ($items as $item) {
                    $item->deleted = true;
                    $mapper->save($item);
                }
                return false;

            }, 9999);
        }
    }


    public static function scopes()
    {
        $scopes = parent::scopes();
        if (static::hasField('deleted')) {

            $scopes['active'] = function ($query) {
                return $query->where(['deleted' => false]);
            };
        }
        return $scopes;
    }
}
