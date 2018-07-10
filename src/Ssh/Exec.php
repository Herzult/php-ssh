<?php declare(strict_types=1);

namespace Ssh;

use InvalidArgumentException;
use RuntimeException;
use function fclose;
use function feof;
use function fgets;
use function fopen;
use function fseek;
use function fstat;
use function ftruncate;
use function fwrite;
use function in_array;
use function preg_match;
use function strlen;
use const SSH2_TERM_UNIT_CHARS;
use const SSH2_TERM_UNIT_PIXELS;

/**
 * Wrapper for ssh2_exec
 *
 * @author Cam Spiers <camspiers@gmail.com>
 * @author Greg Militello <junk@thinkof.net>
 * @author Gildas Quéméner <gildas.quemener@gmail.com>
 */
class Exec extends Subsystem
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
     * @var string
     */
    private $pty = null;

    /**
     * @var int
     */
    private $width = 80;

    /**
     * @var int
     */
    private $height = 25;

    /**
     * @var int
     */
    private $widthHeightType = SSH2_TERM_UNIT_CHARS;

    public function __destruct()
    {
        $this->closeStreams();
    }

    protected function createResource()
    {
        $this->resource = $this->getSessionResource();
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

    public function withPty(?string $pty): self
    {
        $copy = clone $this;
        $copy->pty = $pty;
        return $copy;
    }

    public function withSize(?int $width, int $height = null): self
    {
        $copy = clone $this;
        $copy->width = $width ?? $this->width;
        $copy->height = $height ?? $this->height;
        return $copy;
    }

    public function withDimensionType(int $type)
    {
        if (!in_array($type, [SSH2_TERM_UNIT_CHARS, SSH2_TERM_UNIT_PIXELS])) {
            throw new InvalidArgumentException('Invalid dimension type: ' . $type);
        }

        $copy = clone $this;
        $copy->widthHeightType = $type;
        return $copy;
    }

    public function run(string $cmd, array $env = []): ExecChannel
    {
        // ext-ssh2 does not support getting the exit code, so we need a work around
        $cmd .= ';echo -ne "\n[return_code:$?]"';
        $stdio = ssh2_exec(
            $this->getResource(),
            $cmd,
            $this->pty,
            $env,
            $this->width,
            $this->height,
            $this->widthHeightType
        );

        return new ExecChannel($stdio);
    }
}
