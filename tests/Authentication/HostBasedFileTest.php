<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;
use Ssh\Authentication;

/**
 * @covers \Ssh\Authentication\HostBasedFile
 */
class HostBasedFileTest extends TestCase
{
    public function testShouldConstructWithProperties(): void
    {
        $auth = new HostBasedFile(
            'user',
            'example.com',
            new KeyPair('path/private.key', 'path/public.key'),
            'passPhrase',
            'localUsername'
        );

        $this->assertInstanceOf(Authentication::class, $auth);

        $this->assertSame('user', $auth->username);
        $this->assertSame('example.com', $auth->hostname);
        $this->assertSame('path/public.key', $auth->keyPair->publicKeyFile);
        $this->assertSame('path/private.key', $auth->keyPair->privateKeyFile);
        $this->assertSame('localUsername', $auth->localUsername);
    }
}
