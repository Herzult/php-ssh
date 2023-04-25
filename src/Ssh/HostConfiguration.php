<?php declare(strict_types=1);

namespace Ssh;

/**
 * Configuration of an SSH connection
 *
 * @psalm-import-type SSHCallbacksArray from Configuration
 * @psalm-import-type SSHMethodsArray from Configuration
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
final readonly class HostConfiguration implements Configuration
{
    /**
     * @param SSHMethodsArray $methods Methods as expected in ssh2_connect
     * @param SSHCallbacksArray $callbacks Callbacks as expected by in ssh2_connect
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function __construct(
        private string $host,
        private int $port = 22,
        private array $methods = [],
        private array $callbacks = [],
        private string|null $identity = null,
    ) {
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

    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    public function asArguments(): array
    {
        $args = [
            'host' => $this->host,
            'port' => $this->port,
        ];

        if ($this->methods) {
            $args['methods'] = $this->methods;
        }

        if ($this->callbacks) {
            $args['callbacks'] = $this->callbacks;
        }

        return $args;
    }

    public function getIdentity(): string|null
    {
        return $this->identity;
    }
}
