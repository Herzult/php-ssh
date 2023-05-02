<?php

declare(strict_types=1);

namespace Ssh\OpenSSH;

use Ssh\Authentication;
use Ssh\Authentication\KeyPair;
use Ssh\Configuration;
use Ssh\HostConfiguration;
use Ssh\ProvidesAuthentication;

final class ConfigFile implements Configuration, ProvidesAuthentication
{
    use ConfigDecoratorTrait;
    use PathExpansion;

    public const DEFAULT_SSH_CONFIG = '~/.ssh/config';
    public const DEFAULT_KEY_FILE = '~/.ssh/id_rsa';

    private HostConfig $hostConfig;

    /**
     * @var array<string, array<string, string>>
     */
    private array $data;

    public function __construct(Configuration $hostConfig, string $file = self::DEFAULT_SSH_CONFIG)
    {
        $this->data = (new Parser())->parse($this->expandPath($file));
        $this->hostConfig = $this->findConfig($hostConfig);
        $this->decoratedConfig = $this->hostConfig;
    }

    public static function forHostname(string $hostname, string $file = self::DEFAULT_SSH_CONFIG): self
    {
        return new self(new HostConfiguration($hostname), $file);
    }

    private function prepareIdFile(string $path): KeyPair|null
    {
        if ($path === '') {
            return null;
        }

        return new KeyPair($this->expandPath($path));
    }

    private function findConfig(Configuration $config): HostConfig
    {
        $matches = array_filter($this->data, fn (array $configData): bool => fnmatch($configData['host'], $config->getHost()));
        usort($matches, fn (array $a, array $b): int => strlen($a['host']) <=> strlen($b['host']));
        $result = (count($matches) > 1)? array_merge(...$matches) : ($matches[0] ?? []);

        return new HostConfig(
            new HostConfiguration(
                $result['hostname'] ?? $config->getHost(),
                intval($result['port'] ?? $config->getPort()),
                $config->getMethods(),
                $config->getCallbacks()
            ),
            $result['user'] ?? null,
            $this->prepareIdFile($result['identityfile'] ?? self::DEFAULT_KEY_FILE)
        );
    }

    /**
     * Builds a configuration for the given host
     */
    public function forHost(string|Configuration $host): HostConfig
    {
        return $this->findConfig(
            $host instanceof Configuration
                ? $host
                : new HostConfiguration($host)
        );
    }

    public function getUser(): string|null
    {
        return $this->hostConfig->getUser();
    }

    public function createAuthentication(string|null $passphrase = null, string|null $user = null): Authentication
    {
        return $this->hostConfig->createAuthentication($passphrase, $user);
    }
}
