<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use function fopen;
use RuntimeException;
use function stream_get_contents;
use function tmpfile;

final class ExecChannel
{
    /**
     * @var resource
     */
    private $stdout = null;

    /**
     * @var resource
     */
    private $stderr = null;

    /**
     * @var int
     */
    private $exitCode = null;

    /**
     * @param resource $resource The ssh2_exec resource
     */
    public function __construct($resource)
    {
        $stderr = ssh2_fetch_stream($resource, SSH2_STREAM_STDERR);
        stream_set_blocking($stderr, true);
        stream_set_blocking($resource, true);

        $this->exitCode = $this->processStdout($resource);

        $this->stderr = fopen('php://temp', 'w+');
        stream_copy_to_stream($stderr, $this->stderr);

        fclose($stderr);
        fclose($resource);
    }

    public function __destruct()
    {
        $this->closeStreams();
    }

    public function __toString(): string
    {
        return stream_get_contents($this->stdout);
    }

    private function closeStreams(): void
    {
        if ($this->stdout) {
            fclose($this->stdout);
            $this->stdout = null;
        }

        if ($this->stderr) {
            fclose($this->stderr);
            $this->stderr = null;
        }
    }

    private function processStdout($stream): int
    {
        $this->stdout = fopen('php://temp', 'w+');
        $line = '';

        while (!feof($stream)) {
            $line = fgets($stream);
            fwrite($this->stdout, $line);
        }

        $match = [];

        if (!preg_match('/\[return_code:(\d+)?\]/', $line, $match)) {
            throw new RuntimeException('Unexpected end of STDOUT stream');
        }

        $size = fstat($this->stdout)['size'];
        ftruncate($this->stdout, max($size - (strlen($match[0]) + 1), 0));
        fseek($this->stdout, 0);

        return (int)$match[1];
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @return resource
     */
    public function detachStdout()
    {
        $stream = $this->stdout;
        $this->stdout = null;

        return $stream;
    }

    /**
     * @return resource
     */
    public function detachStderr()
    {
        $stream = $this->stderr;
        $this->stderr = null;

        return $stream;
    }
}
