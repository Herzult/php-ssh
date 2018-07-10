<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\Exception;

use RuntimeException;
use Ssh\Session;

class AuthenticationException extends RuntimeException implements ExceptionInterface
{
    const AUTH_FAILED = 2;

    public static function authenticationFailed(Session $session): self
    {
        return new self(
            sprintf('Failed to authenticate to ssh host "%s"', $session->getConfiguration()->getHost()),
            self::AUTH_FAILED
        );
    }
}
