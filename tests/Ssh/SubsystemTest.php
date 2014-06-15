<?php

namespace Ssh;

/**
 * @covers \Ssh\Subsystem
 */
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The session must be either a Session instance or a SSH session resource.
     */
    public function testInvalidContructorArgumentException()
    {
        new Exec(false);
    }

    public function testGetSessionResourceWillReturnResource()
    {
        $resource = tmpfile();
        $exec = new Exec($resource);

        $this->assertEquals($resource, $exec->getSessionResource());
    }

    public function testGetSessionResourceWillCallSessionGetResource()
    {
        $session = $this->getMock(
            'Ssh\Session', array('getResource'), array(), '', false
        );
        $session->expects($this->once())->method('getResource')->will($this->returnValue('aResource'));

        $exec = new Exec($session);

        $this->assertEquals('aResource', $exec->getSessionResource());
    }
}
