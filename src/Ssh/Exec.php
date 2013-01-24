<?php

namespace Ssh;

/**
 * Wrapper for ssh2_exec
 *
 * @author Cam Spiers <camspiers@gmail.com>
 * @author Greg Militello <junk@thinkof.net>
 */
class Exec extends Subsystem
{
    protected $stream;

    protected function createResource()
    {
        $this->resource = $this->getSessionResource();
    }

    public function run($cmd, $pty = false, $env = null, $width = 80, $height = 25, $width_height_type = SSH2_TERM_UNIT_CHARS)
    {
        $this->stream = ssh2_exec($this->getResource(), $cmd, $pty, $env, $width, $height, $width_height_type);
        stream_set_blocking($this->stream, true);
        return stream_get_contents($this->stream);
    }

    public function getError()
    {
        if (is_resource($this->stream)) {
            return stream_get_contents(ssh2_fetch_stream($this->stream, SSH2_STREAM_STDERR));
        } else {
            return false;
        }
    }
}
