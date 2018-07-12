<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\Exception;

use RuntimeException;
use function sprintf;

class IOException extends RuntimeException implements ExceptionInterface
{
    const READ_ERROR = 64;
    const WRITE_ERROR = 128;

    public static function readError(string $filename, string $remoteHost = null): self
    {
        $msg = 'Failed to read from file "%s"'
             . ($remoteHost? ' on remote host "%s"' : '');

        return new self(
            sprintf($msg, $filename, $remoteHost),
            self::READ_ERROR
        );
    }

    public static function writeError(string $filename, string $remoteHost = null): self
    {
        $msg = 'Failed to write to file "%s"'
             . ($remoteHost? ' on remote host "%s"' : '');

        return new self(
            sprintf($msg, $filename, $remoteHost),
            self::WRITE_ERROR
        );
    }
}
