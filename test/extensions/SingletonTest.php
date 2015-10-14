<?php

class SingletonTest extends PHPUnit_Framework_TestCase {
    
    public function testClass() {
        $instance = SingletonTestClass::singleton();
        $this->assertSame($instance, SingletonTestClass::singleton());

        $sub_instance = SingletonTestSubClass::singleton();
        $this->assertFalse($instance === $sub_instance);
        
        $this->assertSame($instance, SingletonTestClass::singleton());
    }
}

class SingletonTestClass {
    use extensions\Singleton;
}

class SingletonTestSubClass extends SingletonTestClass {
    use extensions\Singleton;
}