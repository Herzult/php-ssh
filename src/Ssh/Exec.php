<?php

namespace Exec;

use RuntimeException;

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
        
        $err_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);

        $dio_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDDIO);

        stream_set_blocking($err_stream, true);
        stream_set_blocking($dio_stream, true);

        $result_err = stream_get_contents($err_stream));
        $result_dio = stream_get_contents($dio_stream));
        
        echo "\nError:\n\n";
        print_r ($result_err);
        echo "\nResult:\n\n";
        print_r ($result_dio);
        echo "\n\n\n";
    }
}
