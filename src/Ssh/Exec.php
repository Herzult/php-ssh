<?php

namespace Ssh;

use RuntimeException;

/**
 * Wrapper for ssh2_exec
 *
 * @author Cam Spiers <camspiers@gmail.com>
 * @author Greg Militello <junk@thinkof.net>
 * @author Gildas Quéméner <gildas.quemener@gmail.com>
 */
class Exec extends Subsystem
{
    protected function createResource()
    {
        $this->resource = $this->getSessionResource();
    }

    public function run($cmd, $pty = null, array $env = array(), $width = 80, $height = 25, $width_height_type = SSH2_TERM_UNIT_CHARS)
    {
        $cmd .= ';echo -ne "[return_code:$?]"';
        $stdout = ssh2_exec($this->getResource(), $cmd, $pty, $env, $width, $height, $width_height_type);
        $stderr = ssh2_fetch_stream($stdout, SSH2_STREAM_STDERR);
        stream_set_blocking($stderr, true);
        stream_set_blocking($stdout, true);

        $output = stream_get_contents($stdout);
        preg_match('/\[return_code:(.*?)\]/', $output, $match);
        if ((int) $match[1] !== 0) {
            throw new RuntimeException(stream_get_contents($stderr), (int) $match[1]);
        }

        return preg_replace('/\[return_code:(.*?)\]/', '', $output);
    }
}
