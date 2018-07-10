<?php declare(strict_types=1);

namespace Ssh\OpenSSH;

use function array_filter;
use function count;
use function fnmatch;
use IteratorAggregate;
use RuntimeException;
use Ssh\Authentication;
use Ssh\Configuration;
use Ssh\HostConfiguration;

/**
 * SSH Config File Configuration
 *
 * @author Cam Spiers <camspiers@gmail.com>
 * @author Axel Helmert <ah@luka.de>
 */
class HostConfig implements Configuration
{
    use ConfigDecoratorTrait;

    const DEFAULT_SSH_CONFIG = '~/.ssh/id_rsa';

    /**
     * @var Configuration
     */
    private $hostConfig;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $privateKeyFile;

    public function __construct(Configuration $hostConfig, string $user = null, string $privateKeyFile = null)
    {
        $this->decoratedConfig = $hostConfig;
        $this->user = $user;
        $this->privateKeyFile = $privateKeyFile;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPrivateKeyFile(): ?string
    {
        return $this->privateKeyFile;
    }

    public function getPublicKeyFile(): ?string
    {
        if ($this->privateKeyFile === null) {
            return null;
        }

        return $this->privateKeyFile . '.pub';
    }

    /**
     * Return an authentication mechanism based on the configuration file
     * @return Authentication
     */
    public function createAuthentication(string $passphrase = null, string $user = null): Authentication
    {
        $user = $user ?? $this->user;

        if ($user === null) {
            throw new RuntimeException("Can not authenticate for '{$this->getHost()}' could not find user to authenticate as");
        }

        if ($this->privateKeyFile) {
            return new Authentication\PublicKeyFile(
                $user,
                $this->getPublicKeyFile(),
                $this->getPrivateKeyFile(),
                $passphrase
            );
        } else {
            return new Authentication\None(
                $user
            );
        }
    }
}
