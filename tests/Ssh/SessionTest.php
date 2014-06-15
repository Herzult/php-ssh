<?php

namespace Ssh;

/**
 * @covers \Ssh\Session
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    public function setUp()
    {
        $this->configuration = $this->getMock('Ssh\Configuration', array('asArguments'), array('my-host'));
        $this->configuration->expects($this->any())
            ->method('asArguments')
            ->will($this->returnValue(array('my-host', 21, array(), array())));
    }

    public function testAuthenticateOnResourceCreation()
    {
        $resource = tmpfile();

        $authentication = $this->getMock('Ssh\Authentication\Password', array(), array('John', 's3cr3t'));
        $authentication->expects($this->once())
            ->method('authenticate')
            ->with($this->equalTo($resource))
            ->will($this->returnValue(true));

        $session = $this->getMock('Ssh\Session', array('connect'), array($this->configuration, $authentication));
        $session->expects($this->once())
            ->method('connect')
            ->will($this->returnValue($resource));

        $session->getResource();
    }

    public function testAuthenticateOnAuthenticationDefinition()
    {
        $resource = tmpfile();

        $session = new Session($this->configuration);

        $property = new \ReflectionProperty($session, 'resource');
        $property->setAccessible(true);
        $property->setValue($session, $resource);

        $authentication = $this->getMock('Ssh\Authentication\Password', array('authenticate'), array('John', 's3cr3t'));
        $authentication->expects($this->once())
            ->method('authenticate')
            ->with($this->equalTo($resource))
            ->will($this->returnValue(true));

        $session->setAuthentication($authentication);
    }

    public function testCreateResourceWillThrowAnExceptionOnConnectionFailure()
    {
        $session = $this->getMock('Ssh\Session', array('connect'), array($this->configuration));
        $session->expects($this->any())
            ->method('connect')
            ->will($this->returnValue(false));

        $method = new \ReflectionMethod($session, 'createResource');
        $method->setAccessible(true);

        $this->setExpectedException('RuntimeException');

        $method->invoke($session);
    }

    public function testCreateResourceWillThrowAnExceptionOnAuthenticationFailure()
    {
        $authentication = $this->getMock('Ssh\Authentication\Password', array('authenticate'), array('John', 's3cr3t'));
        $authentication->expects($this->any())
            ->method('authenticate')
            ->will($this->returnValue(false));

        $session = $this->getMock('Ssh\Session', array('connect'), array($this->configuration, $authentication));
        $session->expects($this->any())
            ->method('connect')
            ->will($this->returnValue(true));

        $method = new \ReflectionMethod($session, 'createResource');
        $method->setAccessible(true);

        $this->setExpectedException('RuntimeException');

        $method->invoke($session);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The subsystem 'does_not_exist' is not supported.
     */
    public function testCreateInvalidSubsystem()
    {
        $session = new Session($this->configuration);

        $session->getSubsystem('does_not_exist');
    }

    public function testGetConfiguration()
    {
        $session = new Session($this->configuration);

        $this->assertEquals($this->configuration, $session->getConfiguration());
    }

    public function testGetSubsystemSftp()
    {
        $session = new Session($this->configuration);

        $this->assertInstanceOf('\Ssh\Sftp', $session->getSftp());
    }

    public function testGetSubsystemPublickey()
    {
        $session = new Session($this->configuration);

        $this->assertInstanceOf('\Ssh\Publickey', $session->getPublickey());
    }

    public function testGetSubsystemExec()
    {
        $session = new Session($this->configuration);

        $this->assertInstanceOf('\Ssh\Exec', $session->getExec());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The authentication over the current SSH connection failed.
     */
    public function testAuthenficationException()
    {
        // A Authentication that will always fail.
        $authentication = $this->getMock('Ssh\Authentication\Password', array(), array('John', 's3cr3t'));
        $authentication->expects($this->once())
                       ->method('authenticate')
                       ->will($this->returnValue(false));

        $session = new Session($this->configuration);

        // We need to inject a resource, to trigger the authentification.
        $resource = tmpfile();
        $property = new \ReflectionProperty($session, 'resource');
        $property->setAccessible(true);
        $property->setValue($session, $resource);

        $session->setAuthentication($authentication);
    }
}
