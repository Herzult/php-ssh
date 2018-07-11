<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ssh\Authentication\PublicKeyFile
 */
class PublicKeyFileTest extends TestCase
{
    public function testClass() {
        $auth = new PublicKeyFile('user', 'path/public.key', 'path/private.key', 'passPhrase');
        $this->assertInstanceOf('\Ssh\Authentication', $auth);

        $this->assertAttributeEquals('user', 'username', $auth);
        $this->assertAttributeEquals('path/public.key', 'publicKeyFile', $auth);
        $this->assertAttributeEquals('path/private.key', 'privateKeyFile', $auth);
        $this->assertAttributeEquals('passPhrase', 'passPhrase', $auth);
    }

    public function testClassOptionals() {
        $auth = new PublicKeyFile('user', 'path/public.key', 'path/private.key');

        $this->assertAttributeEquals('user', 'username', $auth);
        $this->assertAttributeEquals('path/public.key', 'publicKeyFile', $auth);
        $this->assertAttributeEquals('path/private.key', 'privateKeyFile', $auth);
        $this->assertAttributeEquals(null, 'passPhrase', $auth);
    }
}
