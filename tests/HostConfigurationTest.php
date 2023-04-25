<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use function Amp\call;
use function exp;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @psalm-import-type SSHCallbacksArray from Configuration
 * @psalm-import-type SSHMethodsArray from Configuration
 *
 * @covers \Ssh\HostConfiguration
 */
class HostConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $configuration = new HostConfiguration('my-host');

        self::assertSame(22, $configuration->getPort());
        self::assertSame([], $configuration->getCallbacks());
        self::assertSame([], $configuration->getMethods());
        self::assertNull($configuration->getIdentity());
    }

    public function testGetHost(): void
    {
        $configuration = new HostConfiguration('my-host');
        self::assertSame('my-host', $configuration->getHost());
    }

    public function testGetPort(): void
    {
        $configuration = new HostConfiguration('my-host', 1234);
        self::assertSame(1234, $configuration->getPort());
    }

    public function testGetMethods(): void
    {
        $expected = ['hostkey' => 'abcd'];
        $configuration = new HostConfiguration('my-host', methods: $expected);

        self::assertSame($expected, $configuration->getMethods());
    }

    public function testGetCallbacks(): void
    {
        $expected = [
            'ignore' => static function(string $msg): void {},
        ];

        $configuration = new HostConfiguration('my-host', callbacks: $expected);
        self::assertSame($expected, $configuration->getCallbacks());
    }

    /**
     * @dataProvider provideAsArgumentsData
     */
    public function testAsArguments(HostConfiguration $configuration, array $expected): void
    {
        self::assertSame($expected, $configuration->asArguments());
    }

    /**
     * @return iterable<string, array{HostConfiguration, array{host: string, port?: int, methods?: SSHMethodsArray, callbacks?: SSHCallbacksArray}}>
     */
    public static function provideAsArgumentsData(): iterable
    {
        /** @var SSHCallbacksArray $callbacks */
        $callbacks = [
            'ignore' => static function (string $msg): void {},
        ];

        return [
            'defaults' => [
                new HostConfiguration('my-host'),
                ['host' => 'my-host', 'port' => 22]
            ],
            'withPort' => [
                new HostConfiguration('my-host', 2222),
                ['host' => 'my-host', 'port' => 2222]
            ],
            'withMethods' => [
                new HostConfiguration('my-host', methods: ['kex' => ['bar']]),
                ['host' => 'my-host', 'port' => 22, 'methods' => ['kex' => ['bar']]]
            ],
            'withCallbacks' => [
                new HostConfiguration('my-host', callbacks: $callbacks),
                ['host' => 'my-host', 'port' => 22, 'callbacks' => $callbacks],
            ],
        ];
    }
}
