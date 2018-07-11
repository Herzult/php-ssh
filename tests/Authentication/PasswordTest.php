<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ssh\Authentication\Password
 */
class PasswordTest extends TestCase
{
    public function testClass() {
        $pass = new Password('user', 'pass');
        $this->assertInstanceOf('\Ssh\Authentication', $pass);

        $this->assertAttributeEquals('user', 'username', $pass);
        $this->assertAttributeEquals('pass', 'password', $pass);
    }
}
