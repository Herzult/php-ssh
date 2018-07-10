<?php

namespace Ssh;

use InvalidArgumentException, RuntimeException;
use function is_resource;
use LogicException;
use Ssh\Exception\AuthenticationException;


/**
 * SSH session
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Session extends AbstractResourceProvider
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var Subsystem[]
     */
    private $subsystems;

    /**
     * Constructor
     *
     * @param  Configuration  A Configuration instance
     * @param  Authentication An optional Authentication instance
     */
    public function __construct(Configuration $configuration, Authentication $authentication = null)
    {
        $this->configuration  = $configuration;
        $this->authentication = $authentication;
        $this->subsystems     = [];
    }

    /**
     * Defines the authentication.
     *
     * If this is the fist authentication to the instance (not provided via construct) and the session is established,
     * an authentication attempt will be initiated.
     *
     * @throws LogicException When the session is already established with an authentication
     * @throws AuthenticationException When the session ais established and the authentication attempt fails
     */
    public function setAuthentication(Authentication $authentication): void
    {
        if (!is_resource($this->resource)) {
            $this->authentication = $authentication;
            return;
        }

        if ($this->authentication) {
            throw new LogicException('Cannot re-authenticate an already established session');
        }

        $this->authenticate($authentication);
    }

    /**
     * Returns the Sftp subsystem
     *
     * @return Sftp|Subsystem
     */
    public function getSftp(): Sftp
    {
        return $this->getSubsystem('sftp');
    }

    /**
     * Returns the Publickey subsystem
     *
     * @return Publickey|Subsystem
     */
    public function getPublickey(): Publickey
    {
        return $this->getSubsystem('publickey');
    }

    /**
     * Returns the Exec subsystem
     *
     * @return Exec|Subsystem
     */
    public function getExec(): Exec
    {
        return $this->getSubsystem('exec');
    }

    /**
     * Returns the specified subsystem
     *
     * If the subsystem does not exists, this method will attempt to create it
     */
    public function getSubsystem(string $name): Subsystem
    {
        if (!isset($this->subsystems[$name])) {
            $this->createSubsystem($name);
        }

        return $this->subsystems[$name];
    }

    /**
     * Creates the specified subsystem
     *
     * @throws InvalidArgumentException if the specified subsystem is no
     *                                  supported (e.g does not exist)
     */
    protected function createSubsystem(string $name): void
    {
        switch ($name) {
            case 'sftp':
                $subsystem = new Sftp($this);
                break;

            case 'publickey':
                $subsystem = new Publickey($this);
                break;

            case 'exec':
                $subsystem = new Exec($this);
                break;

            default:
                throw new InvalidArgumentException(sprintf('The subsystem \'%s\' is not supported.', $name));
        }

        $this->subsystems[$name] = $subsystem;
    }

    /**
     * Creates the session resource
     *
     * If there is a defined authentication, it will authenticate the session
     *
     * @throws RuntimeException if the connection fail
     */
    protected function createResource(): void
    {
        $this->resource = $this->connect($this->configuration->asArguments());

        if (!is_resource($this->resource)) {
            throw new RuntimeException('The SSH connection failed.');
        }

        if ($this->authentication) {
            $this->authenticate($this->authentication);
        }
    }

    /**
     * Opens a connection with the remote server using the given arguments
     *
     * @return resource
     */
    private function connect(array $arguments)
    {
        return @ssh2_connect(...$arguments);
    }

    /**
     * Authenticates over the current SSH session and using the defined
     * authentication
     *
     * @throws AuthenticationException
     */
    private function authenticate(Authentication $authentication): void
    {
        if (!$authentication->authenticate($this)) {
            throw AuthenticationException::authenticationFailed($this);
        }

        $this->authentication = $authentication;
    }
    
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
