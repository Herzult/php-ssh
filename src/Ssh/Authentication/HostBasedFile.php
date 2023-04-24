<?php

declare(strict_types=1);

namespace Ssh\Authentication;

use Ssh\Authentication;
use Ssh\Session;

/**
 * Host based file authentication
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
final readonly class HostBasedFile implements Authentication
{
    public function __construct(
        public string $username,
        public string $hostname,
        public string $publicKeyFile,
        public string $privateKeyFile,
        public string|null $passPhrase = null,
        public string|null $localUsername = null
    ) {
    }

    public function authenticate(Session $session): bool
    {
        return ssh2_auth_hostbased_file(
            $session->getResource(),
            $this->username,
            $this->hostname,
            $this->publicKeyFile,
            $this->privateKeyFile,
            $this->passPhrase,
            $this->localUsername
        );
    }
}
