<?php
/**
 * LPL
 *
 * PHP Version 5.4
 *
 * @copyright 2014-2015 Luc Chante
 * @license CeCILL
 * @licence http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 *
 * @filesource
 */
namespace extensions {
	/**
	* AnnotationEngine trait
	*
	* This trait provides simples methods to access to annotations
	* 
	* Annotations are specific formated attributes in the doc comment of a
	* class, a property or a method.
	*
	* Accepted annotation format is :
	*  - @MyAnnotation
	*  - @MyAnnotation() // identical to precedent
	*  - @MyAnnotation("string arg", 1, 1.25, true, MyClass::CONTANT)
	*  - @MyAnnotation(group = "admin", ...)
	*
	* In the 2 first cases, it will return true if the annotation is found, false otherwise.
	* The last 2 cases return an array, with association key/value for the last exemple.
	*/
	trait AnnotationEngine {
		
		/**
		* Looks for a class annotation
		*
		* @param string $annotation The annotation to look for
		* @param bool   $recursive  If it looks up into parent classes
		*
		* @return bool|array
		*/
		public static function getClassAnnotation($annotation, $recursive = false) {
			$class = new \ReflectionClass(get_called_class());
			
			do {
				$result = static::getAnnotation($class, $annotation);
				$class = $class->getParentClass();
			} while ($class && $recursive);
			
			return $result;
		}
		
		/**
		 * Looks for a property annotation
		 *
		 * @param string $annotation The annotation to look for
		 * @param string $property   The property in which searching
		 * @param bool   $recursive  If it looks up into parent classes
		 *
		 * @return bool|array
		 */
		public static function getPropertyAnnotation($annotation, $property, $recursive = false) {
			$class = new \ReflectionClass(get_called_class());
			
			do {
				$reflector = $class->getProperty($property);
				$result = static::getAnnotation($reflector, $annotation);
				$class = $class->getParentClass();
			} while ($class && $recursive);
			
			return $result;
		}
		
		/**
		 * Looks for a method annotation
		 *
		 * @param string $annotation The annotation to look for
		 * @param string $method     The method in which searching
		 * @param bool   $recursive  If it looks up into parent classes
		 *
		 * @return bool|array
		 */
		public static function getMethodAnnotation($annotation, $method, $recursive = false) {
			$class = new \ReflectionClass(get_called_class());
			
			do {
				$reflector = $class->getMethod($method);
				$result = static::getAnnotation($reflector, $annotation);
				$class = $class->getParentClass();
			} while ($class && $recursive);
			
			return $result;
		}
		
		/**
		 * @internal
		 */
		private static function parseArgValue($value) {
            $key = null;
            if (strpos("=", $value) !== false) {
                list($key, $value) = explode($value, "=");
                $key = trim($key);
            }
			$value = trim($value);
			
			// Match string '...' | "..."
			if (preg_match('/^(["\'])(.*)\g{-2}$/', $value, $matches)) {
				$value = $matches[2];
			}
			// Match integer
			else if (ctype_digit($value)) {
				$value = intval($value);
			}
			// Match float
			else if (is_numeric($value)) {
				$value = floatval($value);
			}
			// Match boolean
			else if (preg_match('/^(true|false)$/i', $value)) {
				$value = $value == "true";
			}
            // Match constants
			else {
			    $values = explode("|", $value);
			    if (count($values) == 1) {
				    $value = constant($values[0]);
			    }
			    else {
			        $value = array_reduce($values, function($carry, $constant) {
				        return $carry | constant(trim($constant));
			        }, 0);
			    }
			}

			return [ $key, $value ];
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
		private static function getAnnotation($reflector, $annotation) {
			$doc_comment = substr($reflector->getDocComment(), 2, -2);
			
            $pattern = '/^[\s\*]*@' . $annotation . '(\(\))?\s*$/m';
			if (preg_match($pattern, $doc_comment)) {
				return true;
			}
			
			$pattern = '/^[\s\*]*@' . $annotation . '\((.+)\)\s*$/m';
			if (!preg_match($pattern, $doc_comment, $matches)) {
                return false;
            }

            $args = explode(",", $matches[1]);
            $params = [ ];
            foreach ($args as $arg) {
                list($key, $value) = static::parseArgValue($arg);
                if (is_null($key)) {
                    $params[] = $value;
                }
                else {
                    $params[$key] = $value;
                }
            }
			
			return $params;
		}
	}
}

