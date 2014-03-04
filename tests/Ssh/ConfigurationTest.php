<?php

namespace Ssh;

/**
 * @covers \Ssh\Configuration
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultPortIs22()
    {
        $configuration = new Configuration('my-host');

        $this->assertAttributeEquals(22, 'port', $configuration);
    }

    public function testSetHost()
    {
        $configuration = new Configuration('my-host');
        $configuration->setHost('other-host');

        $this->assertAttributeEquals('other-host', 'host', $configuration);
    }

    public function testGetHost()
    {
        $configuration = new Configuration('my-host');

        $this->assertEquals('my-host', $configuration->getHost());
    }

    public function testSetPort()
    {
        $configuration = new Configuration('my-host');
        $configuration->setPort(1234);

        $this->assertAttributeEquals(1234, 'port', $configuration);
    }

    public function testGetPort()
    {
        $configuration = new Configuration('my-host', 1234);

        $this->assertEquals(1234, $configuration->getPort());
    }

    public function testSetMethods()
    {
        $configuration = new Configuration('my-host');
        $configuration->setMethods(array('toto' => 'tata'));

        $this->assertAttributeEquals(array('toto' => 'tata'), 'methods', $configuration);
    }

    public function testGetMethods()
    {
        $configuration = new Configuration('my-host', 22, array('toto' => 'tata'));

        $this->assertEquals(array('toto' => 'tata'), $configuration->getMethods());
    }

    public function testSetCallbacks()
    {
        $configuration = new Configuration('my-host');
        $configuration->setCallbacks(array('toto' => 'tata'));

        $this->assertAttributeEquals(array('toto' => 'tata'), 'callbacks', $configuration);
    }

    public function testGetCallbacks()
    {
        $configuration = new Configuration('my-host', 22, array(), array('toto' => 'tata'));

        $this->assertEquals(array('toto' => 'tata'), $configuration->getCallbacks());
    }

    /**
     * @dataProvider getAsArgumentsData
     */
    public function testAsArguments($configuration, $expected)
    {
        $this->assertEquals($expected, $configuration->asArguments());
    }

    public function getAsArgumentsData()
    {
        return array(
            array(
                new Configuration('my-host'),
                array('my-host', 22, array(), array())
            ),
            array(
                new Configuration('my-host', 2222),
                array('my-host', 2222, array(), array())
            ),
            array(
                new Configuration('my-host', 22, array('toto' => 'tata')),
                array('my-host', 22, array('toto' => 'tata'), array())
            ),
            array(
                new Configuration('my-host', 22, array(), array('toto' => 'tata')),
                array('my-host', 22, array(), array('toto' => 'tata'))
            )
        );
    }
}
