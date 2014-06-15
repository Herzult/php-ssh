<?php


namespace Ssh\Authentication;

/**
 * @covers \Ssh\Authentication\None
 */
class NoneTest extends \PHPUnit_Framework_TestCase
{
    public function testClass() {
        $agent = new None('user');
        $this->assertInstanceOf('\Ssh\Authentication', $agent);

        $this->assertAttributeEquals('user', 'username', $agent);
    }
}
 