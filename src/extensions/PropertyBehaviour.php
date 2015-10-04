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
        use AnnotationEngine;

        /**
         * Handles getter for properties
         */
        public final function __get($property) {
            $getter = static::getPropertyAnnotation("get", "_$property");
            if (!$getter) {
                throw new \ReflectionException (get_class ($this) . " has no property '$property'");
            }

            if ($getter === true) {
                $property = new \ReflectionProperty($this, "_$property");
                $property->setAccessible(true);
                return $property->getValue($this);
            }

            return $this->$getter[0]();
        }

        /**
         * Handles setter for properties
         */
        public final function __set($property, $value) {
            $setter = static::getPropertyAnnotation("set", "_$property");
            if (!$setter) {
                throw new \ReflectionException (get_class ($this) . " has no property '$property'");
            }

            if ($setter === true) {
                $property = new \ReflectionProperty($this, "_$property");
                $property->setAccessible(true);
                $property->setValue($this, $value);
            }
            else {
                $this->$setter[0]($value);
            }
        }
    }
}
