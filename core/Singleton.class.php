<?php

/**
 * Description of Singleton
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-29 02:08:52
 * @copyright joyme.com
 */

namespace Joyme\core;


class Singleton
{
    protected static $instances;

    protected function __construct() { }

    final private function __clone() { }

    /**
     * Returns the singleton instance for the derived class. If the singleton instance
     * has not been created, this method will create it.
     *
     * @return Singleton
     */
    public static function getInstance() {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }
        return self::$instances[$class];
    }

    /**
     * Used in tests only
     * @ignore
     */
    public static function unsetInstance()
    {
        $class = get_called_class();
        unset(self::$instances[$class]);
    }

    /**
     * Sets the singleton instance. For testing purposes.
     * @ignore
     */
    public static function setSingletonInstance($instance)
    {
        $class = get_called_class();
        self::$instances[$class] = $instance;
    }
}
