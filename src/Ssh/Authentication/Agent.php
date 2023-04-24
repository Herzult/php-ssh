<?php declare(strict_types=1);

namespace Ssh\Authentication;

use Ssh\Authentication;
use Ssh\Session;

/**
 * SSH Agent authentication
 *
 * @author Cam Spiers <camspiers@gmail.com>
 */
final readonly class Agent implements Authentication
{
    public function __construct(public string $username)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Session $session): bool
    {
        return ssh2_auth_agent(
            $session->getResource(),
            $this->username
        );
    }
}
