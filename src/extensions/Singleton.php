<?php
/**
 * LPL
 *
 * PHP Version 5.4
 *
 * @copyright 2014 Luc Chante
 * @license CeCILL
 * @licence http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 * 
 * @filesource
 */
namespace extensions {
    /**
     * trait Singleton
     * 
     * This is a simple implemtation of the singleton pattern.
     * 
     * Example :
     * <code>
     * class MySingleton {
     *         use extensions\Singleton;
     * 
     *         public function __construct($arg = "test") {
     *             echo $arg;
     *         }
     * }
     * 
     * $singleton = MySingleton::singleton();
     * // result on the first call : test
     * // or you can use
     * $singleton = MySingleton::singleton("nothing");
     * // result on the first call : nothing
     * $s2 = MySingleton::singleton();
     * var_dump($singleton === $s2);
     * // return : true
     * </code>
     */
    trait Singleton {
        /**
         * @ignore
         */
        private static $instance = null;
        
        /**
         * Return the same instance of the class.
         * 
         * @param mixed $args[optional]
         * @return object
         */
        public static function singleton() {
            $class = new \ReflectionClass(get_called_class());
            $instance = $class->getProperty("instance");
            $instance->setAccessible(true);
            if (is_null($instance->getValue())) {
                switch (func_num_args()) {
                case 0:
                    $instance->setValue($class->newInstance());
                    break;
                default:
                    $instance->setValue($class->newInstanceArgs(func_get_args()));
                }
            }
            return $instance->getValue();
        }
    }
}
