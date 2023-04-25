<?php

namespace Ssh\Authentication;

use SensitiveParameter;
use Ssh\Authentication;
use Ssh\Session;

/**
 * Password Authentication
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
final readonly class Password implements Authentication
{
    public function __construct(
        private string $username,
        #[SensitiveParameter] private string $password,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Session $session): bool
    {
        // This function generates a undocumented warning on authentification failure.
        // TODO: Check if shut-up is still needed with ext-ssh2 >= 1.x
        return @ssh2_auth_password($session->getResource()->resource, $this->username, $this->password);
    }
}
