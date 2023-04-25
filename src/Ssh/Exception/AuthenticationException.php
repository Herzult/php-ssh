<?php

declare(strict_types=1);

namespace Ssh\Exception;

use RuntimeException;
use Ssh\Authentication\KeyPair;
use Ssh\Session;

class AuthenticationException extends RuntimeException implements ExceptionInterface
{
    public const AUTH_FAILED = 2;
    public const BAD_KEY_PAIR = 4;

    public static function authenticationFailed(Session $session): self
    {
        return new self(
            sprintf('Failed to authenticate to ssh host "%s"', $session->configuration->getHost()),
            self::AUTH_FAILED
        );
    }

    public static function badKeyPair(KeyPair $keyPair): self
    {
        return new self(
            sprintf(
                'Unable to load key pair form "%s" and "%s"',
                $keyPair->privateKeyFile,
                $keyPair->publicKeyFile,
            ),
            self::BAD_KEY_PAIR
        );
    }
}
