<?php

namespace Ssh;

use RuntimeException;

class Scp extends Subsystem
{

    /**
     * (PECL ssh2 &gt;= 0.9.0)<br/>
     * Request a file via SCP
     * @link http://php.net/manual/en/function.ssh2-scp-recv.php
     *
     * @param string $remote_file <p>Path to the remote file.</p>
     * @param string $local_file <p>Path to the local file.</p>
     * @return bool true on success or false on failure.
     */
    public function receive($remote_file, $local_file)
    {
        return ssh2_scp_recv($this->getResource(), $remote_file, $local_file);
    }

    /**
     * (PECL ssh2 &gt;= 0.9.0)<br/>
     * Send a file via SCP
     * @link http://php.net/manual/en/function.ssh2-scp-send.php
     *
     * @param string $local_file <p>Path to the local file.</p>
     * @param string $remote_file <p>Path to the remote file.</p>
     * @param int $create_mode [optional] <p>The file will be created with the mode specified by create_mode.</p>
     * @return bool true on success or false on failure.
     */
    public function send($local_file, $remote_file, $create_mode = 0644)
    {
        return ssh2_scp_send($this->getResource(), $local_file, $remote_file, $create_mode);
    }

    public function createResource()
    {
        $resource = $this->getSessionResource();

        if (!is_resource($resource)) {
            throw new RuntimeException('The initialization of the SCP subsystem failed.');
        }

        $this->resource = $resource;
    }

}