<?php

namespace Ssh\Authentication;

use Ssh\Authentication;

/**
 * Public key file authentication
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class PublicKeyFile implements Authentication
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $publicKeyFile;

    /**
     * @var string
     */
    protected $privateKeyFile;

    /**
     * @var null|string
     */
    protected $passPhrase;

    /**
     * Constructor
     *
     * @param  string $username       The authentication username
     * @param  string $publicKeyFile  The path of the public key file
     * @param  string $privateKeyFile The path of the private key file
     * @param  string $passPhrase     An optional pass phrase for the key
     */
    public function __construct(
        string $username,
        string $publicKeyFile,
        string $privateKeyFile,
        string $passPhrase = null
    ) {
        $this->username = $username;
        $this->publicKeyFile = $publicKeyFile;
        $this->privateKeyFile = $privateKeyFile;
        $this->passPhrase = $passPhrase;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate($session): bool
    {
        return ssh2_auth_pubkey_file(
            $session,
            $this->username,
            $this->publicKeyFile,
            $this->privateKeyFile,
            $this->passPhrase
        );
    }
}
