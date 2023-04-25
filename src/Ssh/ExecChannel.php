<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use RuntimeException;
use Throwable;

use function assert;
use function fclose;
use function feof;
use function fopen;
use function is_resource;
use function stream_copy_to_stream;
use function stream_get_contents;


final class ExecChannel
{
    /**
     * @var resource|null
     */
    private mixed $stdout = null;

    /**
     * @var resource|null
     */
    private mixed $stderr = null;

    public readonly int $exitCode;

    /**
     * @param Resource $resource The ssh2_exec resource
     */
    public function __construct(Resource $resource)
    {
        $resource = $resource->resource;
        $stderr = ssh2_fetch_stream($resource, SSH2_STREAM_STDERR);
        stream_set_blocking($stderr, true);
        stream_set_blocking($resource, true);

        try {
            $this->exitCode = $this->processStdout($resource);
            $this->stderr = fopen('php://temp', 'w+');
            stream_copy_to_stream($stderr, $this->stderr);
        } catch (Throwable $err) {
            $this->closeStreams();
            throw $err;
        } finally {
            fclose($stderr);
            fclose($resource);
        }
    }

    public function __destruct()
    {
        $this->closeStreams();
    }

    public function __toString(): string
    {
        return $this->stdout ? stream_get_contents($this->stdout) : '';
    }

    private function closeStreams(): void
    {
        $handles = [$this->stdout, $this->stderr];
        $this->stdout = null;
        $this->stderr = null;

        foreach ($handles as $handle) {
            if ($handle !== null) {
                fclose($handle);
            }
        }
    }

    /**
     * @param resource $stream
     */
    private function processStdout(mixed $stream): int
    {
        $this->stdout = fopen('php://temp', 'w+');
        $lastLine = '';

        while (!feof($stream)) {
            $line = fgets($stream);

            if ($line === false) {
                break;
            }

            $lastLine = $line;
            fwrite($this->stdout, $lastLine);
        }

        $match = [];

        if (!preg_match('/\[return_code:(\d+)?\]/', $lastLine, $match)) {
            throw new RuntimeException('Unexpected end of STDOUT stream');
        }

        $size = fstat($this->stdout)['size'];
        ftruncate($this->stdout, max($size - (strlen($match[0]) + 1), 0));
        fseek($this->stdout, 0);

        return (int)$match[1];
    }

    /**
     * @return resource
     */
    public function stdout(): mixed
    {
        assert(is_resource($this->stdout));
        return $this->stdout;
    }

    /**
     * @return resource
     */
    public function stderr(): mixed
    {
        assert(is_resource($this->stderr));
        return $this->stderr;
    }
}
