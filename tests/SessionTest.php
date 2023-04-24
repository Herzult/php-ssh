<?php

namespace Ssh;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;
use Ssh\Exception\AuthenticationException;
use function fopen;
use function rand;
use function ucfirst;
use const TEST_HOST;

/**
 * @covers \Ssh\Session
 */
class SessionTest extends TestCase
{
    /**
     * @var Configuration|ObjectProphecy
     */
    private $configuration;

    public function setUp(): void
    {
        $this->configuration = $this->prophesize(Configuration::class);
    }

    private function createDummy(Configuration $config = null, Authentication $auth = null): Session
    {
        $config = $config ?? $this->configuration->reveal();

        return new class($config, $auth) extends Session {
            protected function createResource(): void
            {
                $this->resource = fopen('php://temp', 'w');
            }
        };
    }

    public function testAuthenticateOnResourceCreation()
    {
        $authentication = $this->prophesize(Authentication::class);
        $session = new Session(new HostConfiguration(TEST_HOST), $authentication->reveal());

        $authentication->authenticate($session)
            ->shouldBeCalled()
            ->willReturn(true);

        $session->getResource();
    }

    public function testAuthenticateOnAuthenticationDefinition()
    {
        $session = $this->createDummy();
        $session->getResource();

        $authentication = $this->prophesize(Authentication::class);
        $authentication->authenticate($session)
            ->shouldBeCalled()
            ->willReturn(true);

        $session->setAuthentication($authentication->reveal());
    }

    public function testSetAuthenticationForUnconnectedSessionDoesNotAuthenticate()
    {
        $session = $this->createDummy();
        $authentication = $this->prophesize(Authentication::class);
        $authentication->__call('authenticate', [$session])
            ->shouldNotBeCalled();

        $session->setAuthentication($authentication->reveal());
    }

    public function testSetAuthenticationForInitializedSessionThrowsException()
    {
        $authentication = $this->prophesize(Authentication::class);
        $failingAuthentication = $this->prophesize(Authentication::class);
        $session = new Session(new HostConfiguration(TEST_HOST), $authentication->reveal());

        $authentication->authenticate($session)
            ->shouldBeCalled()
            ->willReturn(true);

        $failingAuthentication->authenticate(Argument::any())
            ->shouldNotBeCalled();

        $session->getResource();
        $this->expectException(LogicException::class);
        $session->setAuthentication($failingAuthentication->reveal());
    }


    public function testGetResourceWillThrowExceptionOnConnectionFailure()
    {
        $subject = new Session(new HostConfiguration('localhost', rand(2000, 4000)));

        $this->expectException(RuntimeException::class);
        $subject->getResource();
    }

    public function testCreateResourceWillThrowAnExceptionOnAuthenticationFailure()
    {
        $authentication = $this->prophesize(Authentication::class);
        $authentication->authenticate(Argument::any())
            ->willReturn(false);

        $session = new Session(new HostConfiguration(TEST_HOST), $authentication->reveal());

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(AuthenticationException::AUTH_FAILED);

        $session->getResource();
    }

    public function testSetAuthenticationWillThrowAnExceptionOnAuthenticationFailure()
    {
        $authentication = $this->prophesize(Authentication::class);
        $authentication->authenticate(Argument::any())
            ->willReturn(false);

        $session = new Session(new HostConfiguration(TEST_HOST));
        $session->getResource();

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(AuthenticationException::AUTH_FAILED);

        $session->setAuthentication($authentication->reveal());
    }

    public function testCreateInvalidSubsystemThrowsException()
    {
        $session = $this->createDummy();

        $this->expectException(InvalidArgumentException::class);
        $session->getSubsystem('does_not_exist');
    }

    public function testGetConfiguration()
    {
        $expected = $this->configuration->reveal();
        $session = new Session($expected);

        self::assertSame($expected, $session->getConfiguration());
    }

    public function provideSubsystems(): iterable
    {
        return [
            ['sftp', Sftp::class],
            ['exec', Exec::class],
            ['publickey', Publickey::class],
        ];
    }

    /**
     * @dataProvider provideSubsystems
     */
    public function testWellKnownSubsystem(string $name, string $expectedClass)
    {
        $method = 'get' . ucfirst($name);
        $subject = $this->createDummy();
        $subsystem = $subject->getSubsystem($name);

        self::assertInstanceOf($expectedClass, $subsystem);
        self::assertSame($subsystem, $subject->$method());
    }
}
