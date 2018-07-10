<?php declare(strict_types=1);

namespace Ssh\Authentication;

use Ssh\Authentication;
use Ssh\Session;

/**
 * Host based file authentication
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class HostBasedFile implements Authentication
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $hostname;

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
     * @var null|string
     */
    protected $localUsername;

    /**
     * @param  string $passPhrase     An optional pass phrase for the key
     * @param  string $localUsername  An optional local username. If omitted,
     *                                the username will be used
     */
    public function __construct(
        string $username,
        string $hostname,
        string $publicKeyFile,
        string $privateKeyFile,
        string $passPhrase = null,
        string $localUsername = null
    ) {
        $this->username = $username;
        $this->hostname = $hostname;
        $this->publicKeyFile = $publicKeyFile;
        $this->privateKeyFile = $privateKeyFile;
        $this->passPhrase = $passPhrase;
        $this->localUsername = $localUsername;
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
