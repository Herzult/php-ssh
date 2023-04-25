<?php

declare(strict_types=1);

namespace Ssh\Authentication;

use SensitiveParameter;
use Ssh\Authentication;
use Ssh\Session;

final readonly class PublicKeyFile implements Authentication
{
    public function __construct(
        public string $username,
        private KeyPair $keyPair,
        #[SensitiveParameter] protected string|null $passPhrase = null
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Session $session): bool
    {
        $args = [];

        if ($this->passPhrase !== null) {
            $args[] = $this->passPhrase;
        }

        return ssh2_auth_pubkey_file(
            $session->getResource()->resource,
            $this->username,
            $this->keyPair->publicKeyFile,
            $this->keyPair->privateKeyFile,
            ...$args,
        );
    }
}
