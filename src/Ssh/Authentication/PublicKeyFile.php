<?php

declare(strict_types=1);

namespace Ssh\Authentication;

use SensitiveParameter;
use Ssh\Authentication;
use Ssh\Session;

use function ssh2_auth_pubkey_file;

final readonly class PublicKeyFile implements Authentication
{
    public function __construct(
        public string $username,
        private KeyPairOptions $keyPairs,
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

        foreach ($this->keyPairs as $keyPair) {
            $result = ssh2_auth_pubkey_file(
                $session->getResource()->resource,
                $this->username,
                $keyPair->publicKeyFile,
                $keyPair->privateKeyFile,
                ...$args,
            );

            if ($result) {
                return true;
            }
        }

        return false;
    }
}
