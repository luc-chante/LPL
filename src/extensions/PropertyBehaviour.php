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
         *
         * @param string $property The property name
         * @return mixed
         */
        public final function __get($property) {
            try {
                $getter = static::getPropertyAnnotation("get", $property, true);
                if (!$getter) {
                    throw new \RuntimeException("The property '$property' is not accessible");
                }

                $filter = FILTER_DEFAULT;
                $options = [ ];

                if ($getter !== true) {
                    $filter = isset($getter["filter"]) ? $getter["filter"] : $getter[0];
                    
                    if (count($getter) > 1) {
                        $options = isset($getter["options"]) ? $getter["options"] : $getter[1];
                    }
                }

                return filter_var($this->$property, $filter, $options);
            }
            catch (\ReflectionException $e) {
                throw $e;
            }
        }

        /**
         * Handles setter for properties
         */
        public final function __set($property, $value) {
            try {
                $setter = static::getPropertyAnnotation("set", $property, true);
                if (!$setter) {
                    throw new \RuntimeException("The property '$property' is not writable");
                }

                $filter = FILTER_DEFAULT;
                $options = [ ];

                if ($setter !== true) {
                    $filter = isset($setter["filter"]) ? $setter["filter"] : $setter[0];
                    
                    if (count($setter) > 1) {
                        $options = isset($setter["options"]) ? $setter["options"] : $setter[1];
                    }
                }

                $this->$property = filter_var($value, $filter, $options);
            }
            catch (\ReflectionException $e) {
                throw $e;
            }
        }
    }
}
