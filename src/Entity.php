<?php


namespace Studiow\Spot;

use Spot\Entity as SpotEntity;
use Ramsey\Uuid\Uuid;

abstract class Entity extends SpotEntity
{
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

    /** FIELDS */


    public static function primaryGuid()
    {
        return ['type' => 'guid', 'value' => self::guid(), 'primary' => true];
    }

    public static function primaryInt()
    {
        return ['type' => 'bigint', 'autoincrement' => true, 'primary' => true];
    }

    public static function fieldString($default = null, $length = 255, $params = [])
    {
        return array_merge($params, ['type' => 'string', 'length' => $length, 'default' => $default, 'value' => $default]);
    }

    public static function fieldText($default = null, $params = [])
    {
        return array_merge($params, ['type' => 'text', 'default' => $default, 'value' => $default]);
    }

    public static function fieldDate($now = false, $type = 'datetime', $params = [])
    {
        return array_merge($params, ['type' => $type, 'value' => $now ? new \DateTime() : null]);
    }

    public static function fieldInteger($default = 0, $size = '', $params = [])
    {
        switch ($size) {
            case 'big':
                $type = 'bigint';
                break;
            case 'small':
                $type = 'small';
                break;
            default:
                $type = 'integer';

        }


        return array_merge($params, ['type' => $type, 'default' => $default]);
    }


    public static function fieldGuid($default = null, $params = [])
    {
        return array_merge($params, ['type' => 'guid', 'value' => $default]);
    }

    public static function fieldArray()
    {
        return ['type' => 'json_array', 'value' => []];
    }

    public static function fieldBoolean($default = false)
    {
        return ['type' => 'boolean', 'value' => false];
    }
}