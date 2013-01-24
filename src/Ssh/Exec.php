<?php

namespace Ssh;

/**
 * Remote command execution via ssh
 *
 * @author Greg Militello <junk@thinkof.net>
 */

class Exec extends Subsystem
{
    /**
     * Run $command on remote system
     *
     * @param  string $command The command to be run
     *
     * @return array
     */
    public function run($command)
    {
        $stream = ssh2_exec($this->getResource(), $command);
        
        //$errStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        //stream_set_blocking($errStream, true);
        stream_set_blocking($stream, true);
        //$resultErr = stream_get_contents($errStream);
        $resultDio = stream_get_contents($stream);

        return $resultDio;
    }

    protected function createResource()
    {
        $this->resource = $this->getSessionResource();
    }
}