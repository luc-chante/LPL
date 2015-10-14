<?php

class SubjectTest extends PHPUnit_Framework_TestCase {
    
    public function testClass() {
        $subject = new SubjectTestSubjectClass();
        $o1 = new SubjectTestObbserverClass();
        $o2 = new SubjectTestObbserverClass();
        $o3 = new SubjectTestObbserverClass();
        $o4 = new SubjectTestObbserverClass();

        $subject->attach($o1);
        $subject->attach($o2);
        $subject->attach($o3);
        $subject->attach($o4);

        $subject->notify();
        $this->assertEquals(4, SubjectTestObbserverClass::$updates);

        $subject->detach($o2);
        $subject->detach($o3);

        $subject->notify();
        $this->assertEquals(6, SubjectTestObbserverClass::$updates);

        $subject->detach($o1);
        $subject->detach($o4);

        $subject->notify();
        $this->assertEquals(6, SubjectTestObbserverClass::$updates);
    }
}

class SubjectTestSubjectClass implements SplSubject {
    use extensions\Subject;
}

class SubjectTestObbserverClass implements SplObserver {
    protected static $updates = 0;

    public function update(SplSubject $subject) {
        static::$updates++;
    }
}