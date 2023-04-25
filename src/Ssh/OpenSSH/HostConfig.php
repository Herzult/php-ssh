<?php

declare(strict_types=1);

namespace Ssh\OpenSSH;

use Ssh\Authentication;
use Ssh\Authentication\KeyPair;
use Ssh\Configuration;
use Ssh\ProvidesAuthentication;
use UnexpectedValueException;

use function file_exists;

final readonly class HostConfig implements Configuration, ProvidesAuthentication
{
    use ConfigDecoratorTrait;

    public function __construct(
        Configuration $hostConfig,
        private string|null $user = null,
        private KeyPair|null $keys = null,
    ) {
        $this->decoratedConfig = $hostConfig;
    }

    public function getUser(): string|null
    {
        return $this->user;
    }

    public function getKeyPair(): KeyPair|null
    {
        return $this->keys;
    }

    public function createAuthentication(string|null $passphrase = null, string|null $user = null): Authentication
    {
        $user = $user ?? $this->user;

        if ($user === null) {
            throw new UnexpectedValueException("Can not authenticate for '{$this->getHost()}' could not find user to authenticate as");
        }

        if ($this->keys && $this->keys->exists()) {
            return new Authentication\PublicKeyFile(
                $user,
                $this->keys->publicKeyFile,
                $this->keys->privateKeyFile,
                $passphrase
            );
        } else if ($passphrase !== null && $passphrase !== '') {
            return new Authentication\Password($user, $passphrase);
        } else {
            return new Authentication\None($user);
        }
    }
}
