<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace SshTest;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ssh\AbstractResourceProvider;
use Ssh\Resource;
use SshTest\Fixtures\InvokableDummy;
use function fopen;

/**
 * @covers \Ssh\AbstractResourceProvider
 */
class AbstractResourceProviderTest extends TestCase
{
    use ProphecyTrait;

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

            protected function createResource(): Resource
            {
                return new Resource(($this->provider)());
            }
        };
    }

    public function testResourceIsCreatedIfItDoesNotExist(): void
    {
        $expected = fopen('php://temp', 'w+');
        $subject = $this->createSubject(static fn () => $expected);

        self::assertInstanceOf(Resource::class, $subject->getResource());
        self::assertSame($expected, $subject->getResource()->resource);
    }

    public function testResourceIsCreatedOnlyOnce(): void
    {
        $provider = $this->prophesize(InvokableDummy::class);
        $provider->__call('__invoke', [])
            ->shouldBeCalledTimes(1)
            ->will(function () {
                return fopen('php://temp', 'w');
            });

        $subject = $this->createSubject($provider->reveal());
        $expected = $subject->getResource();

        $this->assertSame($expected, $subject->getResource());
    }
}
