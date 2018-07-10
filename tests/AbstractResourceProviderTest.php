<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace SshTest;

use PHPUnit\Framework\TestCase;
use Ssh\AbstractResourceProvider;
use SshTest\Fixtures\InvokableDummy;
use function fopen;

/**
 * @covers \Ssh\AbstractResourceProvider
 */
class AbstractResourceProviderTest extends TestCase
{
    private function createSubject(callable $provider): AbstractResourceProvider
    {
        return new class($provider) extends AbstractResourceProvider
        {
            /**
             * @var callable
             */
            private $provider;

            public function __construct(callable $provider)
            {
                $this->provider = $provider;
            }

            protected function createResource()
            {
                $this->resource = ($this->provider)();
            }
        };
    }

    public function testResourceIsCreatedIfItDoesNotExist()
    {
        $expected = fopen('php://temp', 'w+');
        $subject = $this->createSubject(function() use ($expected) { return $expected; });

        self::assertSame($expected, $subject->getResource());
    }

    public function testResourceIsCreatedOnlyOnce()
    {
        $provider = $this->prophesize(InvokableDummy::class);
        $provider->__call('__invoke', [])
            ->shouldBeCalledTimes(1)
            ->will(function() {
                return fopen('php://temp', 'w');
            });

        $subject = $this->createSubject($provider->reveal());

        $expected = $subject->getResource();
        $this->assertSame($expected, $subject->getResource());
    }
}
