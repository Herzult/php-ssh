<?php


namespace Ssh\FunctionalTests;


use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;

/**
 * @author Julius Beckmann
 *
 * @group functional
 *
 * @covers \Ssh\Session
 * @covers \Ssh\Authentication\Password
 */
class LoginTest extends \PHPUnit_Framework_TestCase
{

    public function testLoginPassword()
    {
        $configuration = new Configuration('localhost');
        $session = new Session($configuration);

        $authentication = new Password(TEST_USER, TEST_PASSWORD);
        $login = $authentication->authenticate($session->getResource());
        $this->assertTrue($login, 'Authentification failed.');
    }

    public function testLoginPasswordFailed()
    {
        $configuration = new Configuration('localhost');
        $session = new Session($configuration);

        $authentication = new Password(TEST_USER, 'invalid');
        $login = $authentication->authenticate($session->getResource());
        $this->assertFalse($login, 'Authentification should have failed.');
    }
}
 