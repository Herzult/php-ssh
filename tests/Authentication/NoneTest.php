<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ssh\Authentication\None
 */
class NoneTest extends TestCase
{
    public function testClass() {
        $agent = new None('user');
        $this->assertInstanceOf('\Ssh\Authentication', $agent);

        $this->assertAttributeEquals('user', 'username', $agent);
    }
}
