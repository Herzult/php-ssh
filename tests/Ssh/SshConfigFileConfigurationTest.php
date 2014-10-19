<?php

namespace Ssh;

class SshConfigFileConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testParseValidSshConfigFile()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'test');

        $this->assertAttributeEquals(array(
            array(
                'host' => 'hello',
                'hostname' => 'hello.com',
                'port' => '1234'
            ),
            array(
                'host' => 'hello.com',
                'hostname' => 'hello.com',
                'port' => '1234'
            ),
            array(
                'host' => 'test',
                'hostname' => 'test.com'
            ),
            array(
                'host' => 'testuser.com',
                'user' => 'test',
                'identityfile' => 'test'
            ),
            array(
                'host' => 'tamp',
                'hostname' => 'tamp.yo'
            ),
            array(
                'host' => 'identity',
                'user' => 'identity',
                'identityfile' => '~/identity'
            ),
            array(
                'host' => 'ta*',
                'user' => 'bob',
                'port' => '12345',
                'hostname' => 'test.com'
            )
        ), 'configs', $config);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to find configuration for host 'notfound'
     */
    public function testParseSshConfigFileHostNotFound()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'notfound');
    }

    public function testParseInvalidSshConfigFile()
    {
        $exceptions = 0;
        $file = __DIR__ . '/Fixtures/config_invalid';
        try {
            new SshConfigFileConfiguration($file, 'test');
        } catch (\RuntimeException $e) {
            $exceptions++;
            $this->assertEquals("The file '$file' is not parsable at line '1'", $e->getMessage());
        }
        $this->assertEquals(1, $exceptions);
    }

    public function testHostNameFromConfig()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'tamp');
        $this->assertAttributeEquals('tamp.yo', 'host', $config);
    }

    public function testPortFromConfig()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'tamp');
        $this->assertAttributeEquals('12345', 'port', $config);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage The file 'fakefile' does not exist or is not readable
     */
    public function testParseNonExsistantSshConfigFile()
    {
        new SshConfigFileConfiguration('fakefile', 'test');
    }

    /**
     * @covers \Ssh\SshConfigFileConfiguration
     */
    public function testGetAuthentication()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'test');

        $identity = getenv('HOME') . "/.ssh/id_rsa";

        if (file_exists($identity)) {
            $this->assertEquals(new Authentication\PublicKeyFile(
                'test',
                "{$identity}.pub",
                $identity,
                null
            ), $config->getAuthentication(null, 'test'));

        } else {
            $this->assertEquals(new Authentication\None('test'), $config->getAuthentication(null, 'test'));
        }

        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'testuser.com');

        $this->assertEquals(
            new Authentication\PublicKeyFile(
                'test',
                'test.pub',
                'test',
                null
            ),
            $config->getAuthentication()
        );

        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'testuser.com');

        $this->assertEquals(
            new Authentication\PublicKeyFile(
                'otheruser',
                'test.pub',
                'test',
                null
            ),
            $config->getAuthentication(null, 'otheruser')
        );

    }

    public function testIdentityFilePath()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'identity');
        $this->assertAttributeEquals(array(
            'user' => 'identity',
            'identityfile' => getenv('HOME') . '/identity'
        ), 'config', $config);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Can not authenticate for 'test.com' could not find user to authenticate as
     */
    public function testGetAuthenticationFailed()
    {
        $config = new SshConfigFileConfiguration(__DIR__ . '/Fixtures/config_valid', 'test');
        $config->getAuthentication();
    }
}
