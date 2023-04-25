<?php

declare(strict_types=1);

namespace Ssh\Exception;

use RuntimeException;

use function sprintf;

class IOException extends RuntimeException implements ExceptionInterface
{
    const READ_ERROR = 64;
    const WRITE_ERROR = 128;
    const STDIO_ERROR = 256;
    const CONNECT_ERROR = 512;

    public static function readError(string $filename, string|null $remoteHost = null): self
    {
        $msg = 'Failed to read from file "%s"'
             . ($remoteHost? ' on remote host "%s"' : '');

        return new self(
            sprintf($msg, $filename, $remoteHost ?? ''),
            self::READ_ERROR
        );
    }

    public static function writeError(string $filename, string|null $remoteHost = null): self
    {
        $msg = 'Failed to write to file "%s"'
             . ($remoteHost? ' on remote host "%s"' : '');

        return new self(
            sprintf($msg, $filename, $remoteHost ?? ''),
            self::WRITE_ERROR
        );
    }

    public static function stdoutReadError(): self
    {
        return new self('Failure while reading from STDOUT', self::STDIO_ERROR);
    }

    public static function tunnelError(string $host, int $port): self
    {
        return new self(
            sprintf('Failed to create tcp tunnel to "%s:%d"', $host, $port),
            self::CONNECT_ERROR
        );
    }
}
