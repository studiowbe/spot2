<?php


namespace Studiow\Spot;


use Spot\Mapper as SpotMapper;

class Mapper extends SpotMapper
{


    public function all($includeDeleted = false){
        return $this->select('*', $includeDeleted);
    }
    public function select($fields = "*", $includeDeleted = false)
    {
        $entity = $this->entity();
        $rv = parent::select($fields);
        if (!$includeDeleted && $entity::hasField('deleted')) {
            $rv = $rv->active();
        }
        return $rv;
    }
}