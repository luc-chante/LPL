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
			} while ($result === false && $class && $recursive);
			
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
				$result = $reflector->class !== $class->name ? false : static::getAnnotation($reflector, $annotation);
				$class = $class->getParentClass();
			} while ($result === false && $class && $recursive);
			
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
				$result = $reflector->class !== $class->name ? false : static::getAnnotation($reflector, $annotation);
				$class = $class->getParentClass();
			} while ($result === false && $class && $recursive);

			return $result;
		}
		
		/**
		 * Return the result of the search for the given annotation into the given reflector.
		 *
		 * @param \Reflector $reflector  The reflector in which looking for
		 * @param string     $annotation The annotation to look for
		 *
		 * @return mixed
		 */
		private static function getAnnotation(\Reflector $reflector, $annotation) {
            $doc_comment = str_replace("\r", "", substr($reflector->getDocComment(), 2, -2));

            $varname  = '(["\']?)[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\g{-1}';
            $string1  = '"(?:\\\\"|[^"])+"';
            $string2  = "'(?:\\\\'|[^'])+'";
            $integer  = '[+-]?(?:0|[1-9]\d*|0[xX][0-9a-fA-F]+|0b[01]+|0[0-7]+)';
            $float    = '[+-]?(?:\d+|\d*\.\d+|\d+\.\d*)(?:[eE][+-]?\d+)?';
            $constant = '[a-zA-Z_]\w*(::[a-zA-Z_]\w*)?';
            $scalar   = implode("|", [ $string1, $string2, $integer, $float, $constant ]);

            $index = "($varname|$constant)";
            $item  = "[ \t]*(${index}[ \t]*=>[ \t]*)?($scalar|(?&array))[ \t]*";
            $array = "(?P<array>\[$item(,$item)*\])";
            
            $param = "[ \t]*(${varname}[ \t]*=[ \t]*)?($scalar|$array)[ \t]*";

            $key   = "[ \t]*(?P<key>$varname)[ \t]*";
            $value = "[ \t]*(?<value>$scalar|$array)[ \t]*";
            $arg   = "(^|,)($key=)?$value(?=,|$)";

            $pattern = "/^[ \t*]*@$annotation(?P<params>([(].*[)])?)[ \t*]*\$/m";
            if (!preg_match($pattern, $doc_comment, $matches)) {
                return false;
            }
            $arguments = trim($matches["params"], "()");

            if (!preg_match("/^($arg)*$/", $arguments)) {
                throw new \InvalidArgumentException("The annotation '$annotation' has bad format arguments");
            }

            preg_match_all("/$arg/", $arguments, $matches);
            $params = [ ];
            foreach ($matches["key"] as $i => $key) {
                $val = eval("return " . $matches["value"][$i] . ";");
                if (empty($key)) {
                    $params[] = $val;
                }
                else {
                    $params[$key] = $val;
                }
            }
            return empty($params) ? true : $params;
        }
	}
}

