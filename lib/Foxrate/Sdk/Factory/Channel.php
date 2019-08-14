<?php

class Foxrate_Sdk_Factory_Channel
{
    public static function create($type, $id)
    {
        $entity = new Foxrate_Sdk_Entities_Channel();
        $entity->id = $id;
        $entity->type = $type;
        return $entity;
    }
}