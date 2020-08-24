<?php
namespace App\Lib\Coroutine;

// singleton in the same coroutine
abstract class Facade
{
    /**
     * get singleton object in the same coroutine
     * @return Object
     */
    abstract protected static function getObject();

    public static function __callStatic($method, $parameters)
    {
        return static::getObject()->$method(...$parameters);
    }
}