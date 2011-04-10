<?php

namespace Ssh;

class AbstractResourceHolderTest extends \PHPUnit_Framework_TestCase
{
    public function testResourceIsCreatedIfItDoesNotExist()
    {
        $holder = $this->getMockForAbstractClass('Ssh\AbstractResourceHolder');
        $holder->expects($this->once())
            ->method('createResource');

        $holder->getResource();
    }

    public function testResourceIsCreatedOnlyOne()
    {
        $holder = $this->getMockForAbstractClass('Ssh\AbstractResourceHolder');
        $holder->expects($this->never())
            ->method('createResource');

        $property = new \ReflectionProperty($holder, 'resource');
        $property->setAccessible(true);
        $property->setValue($holder, tmpfile());

        $holder->getResource();
    }
}
