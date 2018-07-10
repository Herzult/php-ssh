<?php


namespace Ssh\Authentication;

/**
 * @covers \Ssh\Authentication\Agent
 */
class AgentTest extends \PHPUnit_Framework_TestCase
{
    public function testClass() {
        $agent = new Agent('user');
        $this->assertInstanceOf('\Ssh\Authentication', $agent);

        $this->assertAttributeEquals('user', 'username', $agent);
    }
}
 