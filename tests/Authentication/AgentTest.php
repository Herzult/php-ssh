<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ssh\Authentication\Agent
 */
class AgentTest extends TestCase
{
    public function testClass()
    {
        $agent = new Agent('user');
        $this->assertInstanceOf('\Ssh\Authentication', $agent);

        $this->assertAttributeEquals('user', 'username', $agent);
    }
}
