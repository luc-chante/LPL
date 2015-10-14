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
     * ```php
     * class MySingleton {
     *     use extensions\Singleton;
     * 
     *     public function __construct($arg = "test") {
     *         echo $arg;
     *     }
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
     * ```
     *
     * Every class that should be used as a singleton has to use this trait.
     * ```php
     * class ParentClass {
     *     use extensions\Singleton;
     * }
     * 
     * class ChildClass extends ParentClass {
     * }
     * 
     * // You can get a parent single instance
     * $parent = ParentClass::singleton();
     * // But not a child one
     * $child = ChildClass::singleton();
     * // This will throws a exception (the singleton instance is not accessible)
     *
     * // Good implementation
     * class ChildClass extends ParentClass {
     *    use extensions\Singleton;
     * }
     * ```
     */
    trait Singleton {
        /**
         * @ignore
         */
        private static $instance = null;
        
        /**
         * Return the same instance of the class.
         * 
         * @param mixed $args[optional] Constructor arguments
         * @return object
         */
        public static function singleton() {
            if (is_null(static::$instance)) {
                $class = new \ReflectionClass(get_called_class());
                switch (func_num_args()) {
                    case 0:
                        static::$instance = $class->newInstance();
                        break;
                    default:
                        static::$instance = $class->newInstanceArgs(func_get_args());
                }
            }
            return static::$instance;
        }
    }
}
