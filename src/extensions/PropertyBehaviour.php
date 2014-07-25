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
     * trait PropertyBehaviour
     */
    trait PropertyBehaviour {
        /**
         * Handles getter for properties
         */
        public final function __get($property) {
            $reflection = new \ReflectionProperty($this, $property);
            if (AnnotationEngine::getAnnotation($reflection, "get")) {
                if (method_exists ($this, "_get_$property")) {
                    return call_user_func ([ 
                            $this,
                            "_get_$property" 
                    ]);
                }
                $reflection->setAccessible(true);
                return $reflection->getValue($this);
            }
            throw new \ReflectionException (get_class ($this) . " has no property '$property'");
        }

        /**
         * Handles setter for properties
         */
        public final function __set($property, $value) {
            $reflection = new \ReflectionProperty($this, $property);
            if (AnnotationEngine::getAnnotation($reflection, "set")) {
                if (method_exists ($this, "_set_$property")) {
                    call_user_func ([ 
                            $this,
                            "_set_$property" 
                    ], $value);
                }
                else {
                    $reflection->setAccessible(true);
                    $reflection->setValue($this, $value);
                }
            }
            else {
                throw new \ReflectionException (get_class ($this) . " has no property '$property'");
            }
        }
    }
}
