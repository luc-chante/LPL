<?php

class AnnotationEngineTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The annotation 'ThrowException' has bad format arguments
     */
    public function testClass() {
        $simple = AnnotationEngineTestSubClass::getClassAnnotation("Simple");
        $this->assertFalse($simple);
        $simple = AnnotationEngineTestSubClass::getClassAnnotation("Simple", true);
        $this->assertTrue($simple);
        
        $specific = AnnotationEngineTestSubClass::getClassAnnotation("Specific");
        $this->assertTrue($specific);

        $empty = AnnotationEngineTestSubClass::getClassAnnotation("Empty");
        $this->assertFalse($empty);
        $empty = AnnotationEngineTestSubClass::getClassAnnotation("Empty", true);
        $this->assertTrue($empty);
        
        $db = AnnotationEngineTestSubClass::getClassAnnotation("Db");
        $this->assertFalse($db);
        $db = AnnotationEngineTestSubClass::getClassAnnotation("Db", true);
        $this->assertEquals([ "default", "options" => [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] ], $db);

        $property = AnnotationEngineTestSubClass::getClassAnnotation("property", true);
        $this->assertFalse($property);

        AnnotationEngineTestSubClass::getClassAnnotation("ThrowException", true);
    }

    public function testMethod() {
        $simple = AnnotationEngineTestSubClass::getMethodAnnotation("Simple", "getSomeThing");
        $this->assertFalse($simple);
        $simple = AnnotationEngineTestSubClass::getMethodAnnotation("Simple", "getSomeThing", true);
        $this->assertTrue($simple);
        
        $complex = AnnotationEngineTestSubClass::getMethodAnnotation("Complex", "getSomeThing");
        $this->assertFalse($complex);
        $complex = AnnotationEngineTestSubClass::getMethodAnnotation("Complex", "getSomeThing", true);
        $this->assertEquals([ 1, "two" => "2", [ "foo" => "bar" ] ], $complex);
    }

    public function testProperty() {
        $simple = AnnotationEngineTestSubClass::getPropertyAnnotation("Simple", "property");
        $this->assertFalse($simple);
        $simple = AnnotationEngineTestSubClass::getPropertyAnnotation("Simple", "property", true);
        $this->assertTrue($simple);
        
        $complex = AnnotationEngineTestSubClass::getPropertyAnnotation("Complex", "property");
        $this->assertFalse($complex);
        $complex = AnnotationEngineTestSubClass::getPropertyAnnotation("Complex", "property", true);
        $this->assertEquals([ 1, "two" => "2", [ "foo" => "bar" ] ], $complex);
    }
}

/**
 * @Simple
 * @Empty()
 * @Db("default", options = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ])
 * @Specific(10)
 * @property int $test
 * @ThrowException(test coucou à tous)
 */
class AnnotationEngineTestClass {
    use extensions\AnnotationEngine;

    /**
     * @Simple
     * @Complex(1, two = "2", [ "foo" => "bar" ])
     */
    public $property = null;

    /**
     * @Simple
     * @Complex(1, two = "2", [ "foo" => "bar" ])
     */
    public function getSomeThing() {
    }
}

/**
 * @Specific
 */
class AnnotationEngineTestSubClass extends AnnotationEngineTestClass {

    public function getSomeThingElse() {
    }
}