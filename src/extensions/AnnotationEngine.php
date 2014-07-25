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
     * AnnotationEngine class
     *
     * The class provides simple methods to access annotations on classes, properties and methods.
     * It look for annotations recursively. If the annotation isn't found for this particular class,
     * it looks up to the parent class (if there is one, and if it contains property/method).
     * 
     * Accepted annotation format is :
     *  - @MyAnnotation
     *  - @MyAnnotation() // identical to precedent
     *  - @MyAnnotation("string arg", 1, 1.25, true, MyClass::CONTANT)
     *  - @MyAnnotation(group = "admin", ...)
     * 
     * In the 2 first cases, it will return true if the annotation is found, false otherwise.
     * The last 2 cases return an array, with association key/value for the last exemple.
     * NB: it works only with integer constants.
     */
    class AnnotationEngine {
        
        /**
         * Looks for a class annotation 
         *
         * @param string|\ReflectionClass $class      The class in which searching
         * @param string                  $annotation The annotation to look for
         * 
         * @return bool|array
         */
        public static function getClassAnnotation($class, $annotation) {
            if (!($class instanceof \ReflectionClass)) {
                $class = new \ReflectionClass($class);
            }
            while ($class) {
                $result = static::getAnnotation($class, $annotation);
                if ($result) {
                    return $result;
                }
                $class = $class->getParentClass();
            }
            return false;
        }
        
        /**
         * Looks for a property annotation 
         *
         * @param string|\ReflectionClass    $class[optional] The class containing the property
         * @param string|\ReflectionProperty $property        The property in which searching
         * @param string                     $annotation      The annotation to look for
         * 
         * @return bool|array
         */
        public static function getPropertyAnnotation($property, $annotation) {
            if (func_num_args() == 3) {
                list($class, $property, $annotation) = func_get_args();
                if (!($class instanceof \ReflectionClass)) {
                    $class = new \ReflectionClass($class);
                }
                $property = $class->getProperty($property);
            }
            $class = $property->getDeclaringClass();
            
            try {
				while ($class) {
					$property = $class->getProperty($property->name);
					$result = static::getAnnotation($property, $annotation);
					if ($result) {
						return $result;
					}
					$class = $class->getParentClass();
				}
			}
			catch (ReflectionException $e) {
			}
			return false;
        }
            
        /**
         * Looks for a method annotation 
         *
         * @param string|\ReflectionClass    $class[optional] The class containing the property
         * @param string|\ReflectionMethod   $method          The method in which searching
         * @param string                     $annotation      The annotation to look for
         * 
         * @return bool|array
         */
        public static function getMethodAnnotation($method, $annotation) {
            if (func_num_args() == 3) {
                list($class, $method, $annotation) = func_get_args();
                if (!($class instanceof \ReflectionClass)) {
                    $class = new \ReflectionClass($class);
                }
                $method = $class->getMethod($method);
            }
            $class = $method->getDeclaringClass();
            
            try {
				while ($class) {
					$method = $class->getMethod($method->name);
					$result = static::getAnnotation($method, $annotation);
					if ($result) {
						return $result;
					}
					$class = $class->getParentClass();
				}
			}
			catch (ReflectionException $e) {
			}
			return false;
        }
        
        /**
         * @internal
         */
        private static function parseArgValue($value) {
            $value = trim($value);
            
            // Match string '...' | "..."
            if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                return $matches[1];
            }
            // Match integer
            if (ctype_digit($value)) {
                return intval($value);
            }
            // Match float
            if (is_numeric($value)) {
                return floatval($value);
            }
            // Match boolean
            if (preg_match('/^(true|false)$/i', $value)) {
                return $value == "true";
            }
            $values = explode("|", $value);
            $value = array_reduce($values, function($carry, $constant) {
					$constant = trim($constant);
					if (!defined($constant)) {
						throw new \InvalidArgumentException("$constant is not a valid constant");
					}
					return $carry | constant($constant);
				}, 0);
            return $value;
        }
        
        /**
         * Return the result of the search for the given annotation into the given reflector.
         *
         * @param \ReflectionClass|\ReflectionProperty|\ReflectionMethod
         *                                      $reflector  The reflector in which looking for
         * @param string                        $annotation The annotation to look for
         * 
         * @return mixed
         */
        public static function getAnnotation($reflector, $annotation) {
            if (!method_exists($reflector, "getDocComment")) {
                throw new \InvalidArgumentException(get_class($reflector) . " has no method getDocComment()");
            }
            $doc_comment = $reflector->getDocComment();
            
            $pattern = '/^[\s\*]+@' . $annotation . '\s*(\(\s*\))?\s*$/m';
            if (preg_match($pattern, $doc_comment, $matches)) {
                return true;
            }
            
            $pattern = '/^[\s\*]+@' . $annotation . '\((.+)\)\s*$/m';
            if (preg_match($pattern, $doc_comment, $matches)) {
                $args = explode(",", $matches[1]);
                $args = array_filter($args, function($item) { return strlen(trim($item)) > 0; });
                $params = [ ];
                foreach ($args as $arg) {
                    if (strpos($arg, "=") !== false) {
                        list($arg, $value) = explode("=", $arg, 2);
                        $params[trim($arg)] = static::parseArgValue($value);
                    }
                    else {
                        $params[] = static::parseArgValue($arg);
                    }
                }
                return $params;
            }
            
            return false;
        }
    }
}
