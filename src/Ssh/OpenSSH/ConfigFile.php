<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\OpenSSH;

use function file_exists;
use LogicException;
use Ssh\Authentication;
use Ssh\Configuration;
use Ssh\HostConfiguration;

class ConfigFile implements Configuration
{
    use ConfigDecoratorTrait;

    const DEFAULT_SSH_CONFIG = '~/.ssh/config';
    const DEFAULT_ID_FILE = '~/.ssh/id_rsa';

    private $data = [];

    public function __construct(Configuration $hostConfig, string $file = self::DEFAULT_SSH_CONFIG)
    {
        $this->data = (new Parser())->parse($this->expandPath($file));
        $this->decoratedConfig = $this->findConfig($hostConfig);
    }

    public static function fromHostname(string $hostname, string $file = self::DEFAULT_SSH_CONFIG): self
    {
        return new self(new HostConfiguration($hostname), $file);
    }

    /**
     * Replaces '~/' with users home path
     */
    private function expandPath(string $path): string
    {
        return preg_replace('#^~/#', getenv('HOME') . '/', $path);
    }

    private function prepareIdFile(?string $path): ?string
    {
        if (($path === null) || ($path === '')) {
            return null;
        }

        $path = $this->expandPath($path);

        if (($path === '') || !file_exists($path)) {
            return null;
        }

        return $path;
    }

    private function findConfig(Configuration $config): HostConfig
    {
        $matches = array_filter($this->data, function(array $configData) use ($config): bool {
            return fnmatch($configData['host'], $config->getHost());
        });

        usort($matches, function (array $a, array $b): int {
            return strlen($a['host']) - strlen($b['host']);
        });

        $result = (count($matches) > 1)? array_merge(...$matches) : ($matches[0] ?? []);

        return new HostConfig(
            new HostConfiguration(
                $result['hostname'] ?? $config->getHost(),
                intval($result['port'] ?? $config->getPort()),
                $config->getMethods(),
                $config->getCallbacks()
            ),
            $result['user'] ?? null,
            $this->prepareIdFile($result['identityfile'] ?? self::DEFAULT_ID_FILE)
        );
    }

    /**
     * Builds a configuration for the given host
     */
    public function getConfigForHost(string $host): Configuration
    {
        return $this->findConfig(new HostConfiguration($host));
    }

    public function getUser(): ?string
    {
        return $this->decoratedConfig->getUser();
    }

    /**
     * Return an authentication mechanism based on the configuration file
     * @return Authentication
     */
    public function createAuthenticationMethod(string $passphrase = null, string $user = null): Authentication
    {
        $user = $user ?? $this->decoratedConfig->getUser();

        if ($user === null) {
            throw new LogicException(sprintf(
                'Can not create authentication for "%s" without a user',
                $this->getHost()
            ));
        }

        $privateKey = $this->decoratedConfig->getPrivateKeyFile();

        if ($privateKey) {
            return new Authentication\PublicKeyFile(
                $user,
                $this->decoratedConfig->getPublicKeyFile(),
                $privateKey,
                $passphrase
            );
        } else if ($passphrase !== null) {
            return new Authentication\Password($user, $passphrase);
        } else {
            return new Authentication\None(
                $user
            );
        }
    }


}
