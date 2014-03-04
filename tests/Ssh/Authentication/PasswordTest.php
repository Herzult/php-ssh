<?php


namespace Ssh\Authentication;

/**
 * @covers \Ssh\Authentication\Password
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{
    public function testClass() {
        $pass = new Password('user', 'pass');
        $this->assertInstanceOf('\Ssh\Authentication', $pass);

        $this->assertAttributeEquals('user', 'username', $pass);
        $this->assertAttributeEquals('pass', 'password', $pass);
    }
}
 