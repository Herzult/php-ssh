<?php declare(strict_types=1);

namespace Ssh;

use function array_map;

/**
 * Configuration of an SSH connection
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Configuration
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var array|string[]
     */
    protected $methods;

    /**
     * @var callable[]
     */
    protected $callbacks;

    /**
     * @var string
     */
    protected $identity;

    /**
     * @param callable[] $callbacks Callbacks as expected by in ssh2_connect
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function __construct(
        string $host,
        int $port = 22,
        array $methods = [],
        array $callbacks = [],
        string $identity = null
    ) {
        $this->host      = $host;
        $this->port      = $port;
        $this->methods   = $methods;
        $this->identity  = $identity;

        $this->setCallbacks($callbacks);
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Methods as passed to ssh2_connect()
     *
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * Methods for ssh2_connect()
     *
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * The callbacks for ssh2_connect
     *
     * @param callable[] $callbacks
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function setCallbacks(array $callbacks): void
    {
        $this->callbacks = array_map(
            // Ensure typesafety on the callbacks array
            function(callable $cb): callable {
                return $cb;
            },
            $callbacks
        );
    }

    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * Returns an array of argument designed for the ssh2_connect function
     *
     * @see http://php.net/manual/en/function.ssh2-connect.php
     */
    public function asArguments(): array
    {
        return [
            $this->host,
            $this->port,
            $this->methods,
            $this->callbacks
        ];
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function setIdentity(string $identity): void
    {
        $this->identity = $identity;
    }
}
