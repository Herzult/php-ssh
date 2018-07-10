<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use function exp;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @covers \Ssh\HostConfiguration
 */
class HostConfigurationTest extends TestCase
{
    public function testDefaults()
    {
        $configuration = new HostConfiguration('my-host');

        self::assertSame(22, $configuration->getPort());
        self::assertSame([], $configuration->getCallbacks());
        self::assertSame([], $configuration->getMethods());
        self::assertNull($configuration->getIdentity());
    }

    public function testGetHost()
    {
        $configuration = new HostConfiguration('my-host');
        self::assertSame('my-host', $configuration->getHost());
    }

    public function testGetPort()
    {
        $configuration = new HostConfiguration('my-host', 1234);
        self::assertSame(1234, $configuration->getPort());
    }

    public function testGetMethods()
    {
        $expected = ['foo' => 'bar'];
        $configuration = new HostConfiguration('my-host', 22, $expected);

        self::assertSame($expected, $configuration->getMethods());
    }

    public function testGetCallbacks()
    {
        $expected = [
            'foo' => function() {}
        ];

        $configuration = new HostConfiguration('my-host', 22, [], $expected);
        self::assertSame($expected, $configuration->getCallbacks());
    }

    public function testNonCallableCallbacksThrowsTypeError()
    {
        $this->expectException(TypeError::class);

        new HostConfiguration('my-host', 22, [], [
            'foo' => false
        ]);
    }

    /**
     * @dataProvider provideAsArgumentsData
     */
    public function testAsArguments(HostConfiguration $configuration, array $expected)
    {
        self::assertSame($expected, $configuration->asArguments());
    }

    public function provideAsArgumentsData(): iterable
    {
        $callbacks = [
            'foo' => function() {}
        ];

        return [
            'defaults' => [
                new HostConfiguration('my-host'),
                ['my-host', 22, [], []]
            ],
            'withPort' => [
                new HostConfiguration('my-host', 2222),
                ['my-host', 2222, [], []]
            ],
            'withMethods' => [
                new HostConfiguration('my-host', 22, ['foo' => 'bar']),
                ['my-host', 22, ['foo' => 'bar'], []]
            ],
            'withCallbacks' => [
                new HostConfiguration('my-host', 22, [], $callbacks),
                ['my-host', 22, [], $callbacks]
            ],
        ];
    }
}
