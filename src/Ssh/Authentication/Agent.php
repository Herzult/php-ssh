<?php declare(strict_types=1);

namespace Ssh\Authentication;

use Ssh\Authentication;

/**
 * SSH Agent authentication
 *
 * @author Cam Spiers <camspiers@gmail.com>
 */
class Agent implements Authentication
{
    /**
     * @var string
     */
    protected $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate($session): bool
    {
        return ssh2_auth_agent(
            $session,
            $this->username
        );
    }
}
