<?php

namespace Ssh;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \Ssh\Subsystem
 */
class SubsystemTest extends TestCase
{
    public function testSessionResourceIsNotUsedOnCreation()
    {
        $session = $this->prophesize(Session::class);
        $session->getResource()
            ->shouldNotBeCalled();

        new class($session->reveal()) extends Subsystem {
            protected function createResource()
            {}
        };
    }

    public function testGetSessionResourceWillReturnResourceFromSessionInstance()
    {
        $resource = tmpfile();
        $session = $this->prophesize(Session::class);
        $session->getResource()
            ->shouldBeCalled()
            ->willReturn($resource);

        $subject = new class($session->reveal()) extends Subsystem {
            protected function createResource()
            {}
        };

        $method = new ReflectionMethod(Subsystem::class, 'getSessionResource');
        $method->setAccessible(true);
        $result = $method->invoke($subject);

        self::assertSame($resource, $result);
    }
}
