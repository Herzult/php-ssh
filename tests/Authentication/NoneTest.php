<?php


namespace Ssh\Authentication;

use PHPUnit\Framework\TestCase;
use Ssh\Authentication;

/**
 * @covers \Ssh\Authentication\None
 */
class NoneTest extends TestCase
{
    public function testShouldPopulateUser(): void
    {
        $agent = new None('user');

        $this->assertInstanceOf(Authentication::class, $agent);
        $this->assertSame('user', $agent->username);
    }
}
