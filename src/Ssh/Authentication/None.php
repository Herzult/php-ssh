<?php

declare(strict_types=1);

namespace Ssh\Authentication;

use Ssh\Authentication;
use Ssh\Session;

final readonly class None implements Authentication
{
    /**
     * Constructor
     */
    public function __construct(public string $username)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Session $session): bool
    {
        return ssh2_auth_none($session->getResource()->resource, $this->username) === true;
    }
}
