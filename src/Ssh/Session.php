<?php

declare(strict_types=1);

namespace Ssh;

use LogicException;
use RuntimeException;
use Ssh\Authentication\FallbackAggregate;
use Ssh\Exception\AuthenticationException;

use function is_resource;


/**
 * SSH session
 */
class Session extends AbstractResourceProvider
{
    private Sftp|null $sftp = null;
    private Publickey|null $publickey = null;
    private Exec|null $exec = null;
    private Tunnel|null $tunnel = null;

    public function __construct(
        public readonly Configuration $configuration,
        private Authentication|null $authentication = null
    ) {
    }

    /**
     * Defines the authentication.
     *
     * If this is the fist authentication to the instance (not provided via construct) and the session is established,
     * an authentication attempt will be initiated.
     *
     * @throws LogicException When the session is already established with an authentication
     * @throws AuthenticationException When the session is established and the authentication attempt fails
     */
    public function authenticateWith(Authentication $authentication): void
    {
        if ($this->resource && $this->authentication) {
            throw new LogicException('Cannot re-authenticate an already established session');
        }

        $this->authentication = $authentication;

        if ($this->resource) {
            $this->authenticate();
        }
    }

    public function sftp(): Sftp
    {
        if (!$this->sftp) {
            $this->sftp = new Sftp($this);
        }

        return $this->sftp;
    }

    public function publickey(): Publickey
    {
        if (!$this->publickey) {
            $this->publickey = new Publickey($this);
        }

        return $this->publickey;
    }

    public function exec(): Exec
    {
        if (!$this->exec) {
            $this->exec = new Exec($this);
        }

        return $this->exec;
    }

    public function tunnel() : Tunnel
    {
        if (!$this->tunnel) {
            $this->tunnel = new Tunnel($this);
        }

        return $this->tunnel;
    }

    /**
     * Creates the session resource
     *
     * If there is a defined authentication, it will authenticate the session
     *
     * @throws RuntimeException if the connection fail
     */
    protected function createResource(): Resource
    {
        $resource = $this->connect();

        if (!is_resource($resource)) {
            throw new RuntimeException('The SSH connection failed.');
        }

        $this->resource = new Resource($resource);
        $this->authenticate();

        return $this->resource;
    }

    /**
     * Opens a connection with the remote server using the given arguments
     *
     * @return resource|false
     */
    private function connect(): mixed
    {
        return @ssh2_connect(...$this->configuration->asArguments());
    }

    /**
     * Authenticates over the current SSH session and using the defined
     * authentication
     *
     * @throws AuthenticationException
     */
    private function authenticate(): void
    {
        if ($this->authentication && !$this->authentication->authenticate($this)) {
            throw AuthenticationException::authenticationFailed($this);
        }
    }
}
