<?php

declare(strict_types=1);

namespace Ssh\Authentication;

use SensitiveParameter;
use Ssh\Authentication;
use Ssh\Exception\AuthenticationException;
use Ssh\Session;

final readonly class HostBasedFile implements Authentication
{
    public function __construct(
        public string $username,
        public string $hostname,
        public KeyPair $keyPair,
        #[SensitiveParameter] private string|null $passPhrase = null,
        public string|null $localUsername = null
    ) {
    }

    public function authenticate(Session $session): bool
    {
        if (!$this->keyPair->exists()) {
            throw AuthenticationException::badKeyPair($this->keyPair);
        }

        /** @var array{passphrase?: string, local_username?: string} $args*/
        $args = [];

        if ($this->passPhrase) {
            $args['passphrase'] = $this->passPhrase;
        }

        if ($this->localUsername) {
            $args['local_username'] = $this->localUsername;
        }

        return ssh2_auth_hostbased_file(
            $session->getResource()->resource,
            $this->username,
            $this->hostname,
            $this->keyPair->publicKeyFile,
            $this->keyPair->privateKeyFile,
            ...$args,
        );
    }
}
