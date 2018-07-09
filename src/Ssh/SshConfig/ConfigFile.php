<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\SshConfig;

use Ssh\HostConfiguration;

class ConfigFile
{
    const DEFAULT_SSH_CONFIG = '~/.ssh/id_rsa';

    private $data = [];

    private $matched;

    public function __construct(Configuration $wrapped, string $file = self::DEFAULT_SSH_CONFIG)
    {
        $this->data = (new Parser())->parse($this->expandPath($file));
        $this->matched = $this->findConfig($wrapped);
    }

    /**
     * Replaces '~/' with users home path
     */
    private function expandPath(string $path): string
    {
        return preg_replace('#^~/#', getenv('HOME') . '/', $path);
    }

    private function findConfig(Configuration $config): SshConfiguration
    {
        $matches = array_filter($this->data, function(array $config) use ($config): bool {
            return fnmatch($config['host'], $config->getHost());
        });

        usort($matches, function (array $a, array $b): int {
            return strlen($a['host']) - strlen($b['host']);
        });

        $result = (count($matches) > 1)? array_merge(...$matches) : ($matches[0] ?? []);

        return new SshConfiguration(
            new HostConfiguration(
                $result['hostname'] ?? $config->getHost(),
                intval($result['port'] ?? $config->getPort()),
                $config->getMethods(),
                $config->getCallbacks()
            ),
            $result['user'] ?? null,
            $result['identityfile'] ?? null
        );
    }

    /**
     * Builds a configuration for the given host
     */
    public function getConfigForHost(string $host): Configuration
    {
        return $this->findConfig(new HostConfiguration($host));
    }

    /**
     * Return an authentication mechanism based on the configuration file
     * @param  string|null $passphrase
     * @param  string|null $user
     * @return PublicKeyFile|None
     */
    public function getAuthentication($passphrase = null, $user = null)
    {
        if (is_null($user) && !isset($this->config['user'])) {
            throw new RuntimeException("Can not authenticate for '{$this->host}' could not find user to authenticate as");
        }
        $user = $user ?: $this->config['user'];
        if (isset($this->config['identityfile'])) {
            return new Authentication\PublicKeyFile(
                $user,
                $this->config['identityfile'] . '.pub',
                $this->config['identityfile'],
                $passphrase
            );
        } else {
            return new Authentication\None(
                $user
            );
        }
    }

}
