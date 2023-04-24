<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;
use Ssh\Authentication;

/**
 * @covers \Ssh\Authentication\Agent
 */
class AgentTest extends TestCase
{
    public function testShouldImplementAuthentication(): void
    {
        $agent = new Agent('user');
        self::assertInstanceOf(Authentication::class, $agent);
    }

    public function testShouldReflectTheUser()
    {
        $agent = new Agent('user');
        $this->assertSame('user', $agent->username);
    }
}
