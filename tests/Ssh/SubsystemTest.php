<?php

namespace Ssh;

class SubsystemTest extends \PHPUnit_Framework_TestCase
{
    public function testSessionResourceIsNotUsedOnCreation()
    {
        $session = $this->getMock(
            'Ssh\Session', array(), array(), '', false
        );

        $session->expects($this->never())->method('getResource');

        $subsystem = $this->getMockForAbstractClass(
            'Ssh\Subsystem', array($session)
        );
    }
}
