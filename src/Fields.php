<?php


namespace Studiow\Spot;

use Ramsey\Uuid\Uuid;

class Fields
{
    public static function primaryGuid()
    {
        return ['type' => 'guid', 'value' => Uuid::uuid4()->toString(), 'primary' => true];
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
            case 'bigint':
                $type = 'bigint';
                break;
            case 'small':
            case 'smallint':
                $type = 'smallint';
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