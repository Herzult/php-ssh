<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ssh\Authentication\HostBasedFile
 */
class HostBasedFileTest extends TestCase
{
    public function testClass() {
        $auth = new HostBasedFile('user', 'example.com', 'path/public.key', 'path/private.key', 'passPhrase', 'localUsername');
        $this->assertInstanceOf('\Ssh\Authentication', $auth);

        $this->assertAttributeEquals('user', 'username', $auth);
        $this->assertAttributeEquals('example.com', 'hostname', $auth);
        $this->assertAttributeEquals('path/public.key', 'publicKeyFile', $auth);
        $this->assertAttributeEquals('path/private.key', 'privateKeyFile', $auth);
        $this->assertAttributeEquals('passPhrase', 'passPhrase', $auth);
        $this->assertAttributeEquals('localUsername', 'localUsername', $auth);
    }

    public function testClassOptionals() {
        $auth = new HostBasedFile('user', 'example.com', 'path/public.key', 'path/private.key');

        $this->assertAttributeEquals('user', 'username', $auth);
        $this->assertAttributeEquals('example.com', 'hostname', $auth);
        $this->assertAttributeEquals('path/public.key', 'publicKeyFile', $auth);
        $this->assertAttributeEquals('path/private.key', 'privateKeyFile', $auth);
        $this->assertAttributeEquals(null, 'passPhrase', $auth);
        $this->assertAttributeEquals(null, 'localUsername', $auth);
    }
}
