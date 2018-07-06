<?php

namespace Ssh;

use InvalidArgumentException, RuntimeException;

/**
 * SSH session
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Session extends AbstractResourceHolder
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Authentication
     */
    protected $authentication;

    /**
     * @var Subsystem[]
     */
    protected $subsystems;

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
     * an authentication attempt will be intiated.
     */
    public function setAuthentication(Authentication $authentication): void
    {
        $isFirstAuthentication = (null === $this->authentication);
        $this->authentication = $authentication;

        if ($isFirstAuthentication && is_resource($this->resource)) {
            $this->authenticate();
        }
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
        $resource = $this->connect($this->configuration->asArguments());

        if (!is_resource($resource)) {
            throw new RuntimeException('The SSH connection failed.');
        }

        $this->resource = $resource;

        if (null !== $this->authentication) {
            $this->authenticate();
        }
    }

    /**
     * Opens a connection with the remote server using the given arguments
     *
     * @return resource
     */
    private function connect(array $arguments)
    {
        return ssh2_connect(...$arguments);
    }

    /**
     * Authenticates over the current SSH session and using the defined
     * authentication
     *
     * @throws RuntimeException on authentication failure
     */
    private function authenticate(): void
    {
        $authenticated = $this->authentication->authenticate($this->resource);

        if (!$authenticated) {
            throw new RuntimeException('The authentication over the current SSH connection failed.');
        }
    }
    
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
