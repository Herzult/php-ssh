<?php

namespace Ssh;

use RuntimeException;

/**
 * Wrapper for ssh2_exec
 *
 * @author Cam Spiers <camspiers@gmail.com>
 */
class Exec extends Subsystem
{
    protected $cmd;
    protected $pty;
    protected $env;
    protected $width;
    protected $height;
    protected $width_height_type;

    public function __construct($session, $cmd, $pty = false, $env = null, $width = 80, $height = 25, $width_height_type = SSH2_TERM_UNIT_CHARS)
    {
        parent::__construct($session);
        $this->cmd = $cmd;
        $this->pty = $pty;
        $this->env = $env;
        $this->width = $width;
        $this->height = $height;
        $this->width_height_type = $width_height_type;
    }

    protected function createResource()
    {
        $stream = ssh2_exec($this->getSessionResource(), $this->cmd, $this->pty, $this->env, $this->width, $this->height, $this->width_height_type);
        stream_set_blocking($stream, true);

        if (!is_resource($stream)) {
            throw new RuntimeException('The initialization of the Exec subsystem failed.');
        }

        $this->resource = $stream;
    }

    public function run()
    {
        return stream_get_contents($this->getResource());
    }

    public function getError()
    {
        return stream_get_contents(ssh2_fetch_stream($this->getResource(), SSH2_STREAM_STDERR));
    }
}
