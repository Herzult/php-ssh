<?php

declare(strict_types=1);

namespace Ssh\OpenSSH;

use Ssh\Configuration;

/**
 * @psalm-import-type SSHCallbacksArray from Configuration
 * @psalm-import-type SSHMethodsArray from Configuration
 */
trait ConfigDecoratorTrait
{
    private Configuration $decoratedConfig;

    public function getHost(): string
    {
        return $this->decoratedConfig->getHost();
    }

    public function getPort(): int
    {
        return $this->decoratedConfig->getPort();
    }

    public function getIdentity(): string | null
    {
        return $this->decoratedConfig->getIdentity();
    }

    /**
     * @return SSHMethodsArray
     */
    public function getMethods(): array
    {
        return $this->decoratedConfig->getMethods();
    }

    /**
     * @return SSHCallbacksArray
     */
    public function getCallbacks(): array
    {
        return $this->decoratedConfig->getCallbacks();
    }

    /**
     * @return array{host: string, port?: int, methods?: SSHMethodsArray, callbacks?: SSHCallbacksArray}
     */
    public function asArguments(): array
    {
        return $this->decoratedConfig->asArguments();
    }
}
