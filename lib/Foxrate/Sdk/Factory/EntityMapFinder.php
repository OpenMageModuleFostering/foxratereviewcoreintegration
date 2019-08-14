<?php


class Foxrate_Sdk_Factory_EntityMapFinder
{
    const MAPPER = 'Mapper';

    public static function findEntityMapper($entity)
    {
        $entityClass = get_class($entity);
        $mapper = $entityClass . self::MAPPER;

        if (!class_exists($entity)) {
            throw new RuntimeException(
                sprintf('Class %s not exist', $mapper)
            );
        }

        return $mapper;
    }
}
 