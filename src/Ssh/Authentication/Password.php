<?php

namespace Ssh\Authentication;

use Ssh\Authentication;
use Ssh\Session;

/**
 * Password Authentication
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Password implements Authentication
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Session $session): bool
    {
        // This function generates a undocumented warning on authentification failure.
        // TODO: Check if shut-up is still needed with ext-ssh2 >= 1.x
        return @ssh2_auth_password($session->getResource(), $this->username, $this->password);
    }
}
