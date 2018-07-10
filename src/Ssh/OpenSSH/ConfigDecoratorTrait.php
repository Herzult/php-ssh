<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\OpenSSH;

use Ssh\Configuration;

trait ConfigDecoratorTrait
{
    /**
     * @var Configuration
     */
    private $decoratedConfig;


    public function getHost(): string
    {
        return $this->decoratedConfig->getHost();
    }

    public function getPort(): int
    {
        return $this->decoratedConfig->getPort();
    }

    public function getIdentity(): ?string
    {
        return $this->decoratedConfig->getIdentity();
    }

    public function getMethods(): array
    {
        return $this->decoratedConfig->getMethods();
    }

    public function getCallbacks(): array
    {
        $this->decoratedConfig->getCallbacks();
    }

    public function asArguments(): array
    {
        return $this->decoratedConfig->asArguments();
    }
}
