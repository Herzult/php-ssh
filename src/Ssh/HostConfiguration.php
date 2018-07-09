<?php declare(strict_types=1);

namespace Ssh;

use function array_map;

/**
 * Configuration of an SSH connection
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class HostConfiguration implements Configuration
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var array|string[]
     */
    private $methods;

    /**
     * @var callable[]
     */
    private $callbacks;

    /**
     * @var string
     */
    private $identity;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @param callable[] $callbacks Callbacks as expected by in ssh2_connect
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function __construct(
        string $host,
        int $port = null,
        array $methods = [],
        array $callbacks = [],
        string $identity = null,
        Authentication $authentication = null
    ) {
        $this->host = $host;
        $this->port = $port ?? 22;
        $this->methods = $methods;
        $this->identity = $identity;
        $this->authentication = $authentication;

        $this->setCallbacks($callbacks);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
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
    private function setCallbacks(array $callbacks): void
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
}
