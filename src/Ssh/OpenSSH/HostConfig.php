<?php

declare(strict_types=1);

namespace Ssh\OpenSSH;

use Ssh\Authentication;
use Ssh\Authentication\KeyPair;
use Ssh\Authentication\KeyPairOptions;
use Ssh\Configuration;
use Ssh\ProvidesAuthentication;
use UnexpectedValueException;

use function file_exists;

final class HostConfig implements Configuration, ProvidesAuthentication
{
    use ConfigDecoratorTrait;

    private KeyPairOptions $keys;

    public function __construct(
        Configuration $hostConfig,
        private string|null $user = null,
        KeyPairOptions|KeyPair|null $keys = null,
    ) {
        $this->decoratedConfig = $hostConfig;
        $this->keys = $keys instanceof KeyPairOptions ? $keys : KeyPairOptions::fromKeyPair($keys);
    }

    public function getUser(): string|null
    {
        return $this->user;
    }

    public function getKeyPair(): KeyPair|KeyPairOptions
    {
        return $this->keys;
    }

    public function createAuthentication(string|null $passphrase = null, string|null $user = null): Authentication
    {
        $user = $user ?? $this->user;

        if ($user === null) {
            throw new UnexpectedValueException("Can not authenticate for '{$this->getHost()}' could not find user to authenticate as");
        }

        $authentication = null;

        if ($this->keys->count()) {
            $authentication = new Authentication\PublicKeyFile(
                $user,
                $this->keys,
                $passphrase
            );
        }

        if ($passphrase !== null && $passphrase !== '') {
            $authentication = Authentication\FallbackAggregate::aggregate(
                new Authentication\Password($user, $passphrase),
                $authentication,
            );
        }

        return $authentication ?? new Authentication\None($user);
    }
}
