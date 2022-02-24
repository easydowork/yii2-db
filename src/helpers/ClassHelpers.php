<?php
namespace easydowork\db\helpers;

use easydowork\db\base\InvalidConfigException;

class ClassHelpers
{
    /**
     * createObject
     * @param       $type
     * @param array $params
     * @throws InvalidConfigException
     */
    public static function createObject($type, array $params = [])
    {
        if (is_string($type)) {
            return static::get($type, $params);
        }

        if (!is_array($type)) {
            throw new InvalidConfigException('Unsupported configuration type: ' . gettype($type));
        }

        if (isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return static::get($class, $type);
        }

        throw new InvalidConfigException('Object configuration must be an array containing a "class" or "__class" element.');
    }

    /**
     * get
     * @param       $class
     * @param array $params
     * @return object
     */
    public static function get($class, array $params = [])
    {
        static $classMap = [];

        $key = md5($class.serialize($params));

        if(empty($classMap[$key])){
            $classMap[$key] = new $class($params);
        }

        return $classMap[$key];
    }

}
