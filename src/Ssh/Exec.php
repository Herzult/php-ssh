<?php

declare(strict_types=1);

namespace Ssh;

use InvalidArgumentException;
use RuntimeException;

use function in_array;
use function is_resource;

use const SSH2_TERM_UNIT_CHARS;
use const SSH2_TERM_UNIT_PIXELS;

class Exec extends Subsystem
{
    private string|null $pty = null;
    private int $width = 80;
    private int $height = 25;
    private int $widthHeightType = SSH2_TERM_UNIT_CHARS;

    protected function createResource(): Resource
    {
        return $this->getSessionResource();
    }

    public function withPty(string $pty): self
    {
        $copy = clone $this;
        $copy->pty = $pty;
        return $copy;
    }

    public function withoutPty(): self
    {
        $copy = clone $this;
        $copy->pty = null;
        return $copy;
    }

    public function withSize(?int $width, int $height = null): self
    {
        $copy = clone $this;
        $copy->width = $width ?? $this->width;
        $copy->height = $height ?? $this->height;
        return $copy;
    }

    public function withDimensionType(int $type): self
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
        /** @psalm-var string $pty Work around bad type def of ssh2 ext */
        $pty = $this->pty;

        $stdio = ssh2_exec(
            $this->getResource()->resource,
            // ext-ssh2 does not support getting the exit code, so we need a work around
            $cmd . ';echo -ne "\n[return_code:$?]"',
            $pty,
            $env,
            $this->width,
            $this->height,
            $this->widthHeightType
        );

        if (!is_resource($stdio)) {
            throw new RuntimeException('Failed to execute remote command: ' . $cmd);
        }

        return new ExecChannel(new Resource($stdio));
    }
}
