<?php

namespace Ssh;


use RuntimeException;

/**
 * Wrapper for ssh2_scp_send and ssh2_scp_recv
 *
 * @author Fraser Reed <fraser.reed@gmail.com>
 */
class Scp extends Subsystem
{
    protected function createResource()
    {
        $this->resource = $this->getSessionResource();
    }

    /**
     * Push the specified local file to the specified remote file
     *
     * @param  string $local   The local filename
     * @param  string $distant The distant filename
     * @param int     $createMode
     *
     * @throws \RuntimeException
     * @return boolean TRUE on success
     */
    public function send( $local, $distant, $createMode = 0644 )
    {
        if( ssh2_scp_send( $this->getResource(), $local, $distant, $createMode ) )
        {
            throw new RuntimeException( 'There was an error pulling the remote file.' );
        }

        return true;
    }

    /**
     * Pull the specified remote file to the specified local file
     *
     * @param  string $distant The distant filename
     * @param  string $local   The local filename
     *
     * @throws \RuntimeException
     * @return boolean TRUE on success
     */
    public function receive( $distant, $local )
    {
        if( ssh2_scp_recv( $this->getResource(), $distant, $local ) )
        {
            throw new RuntimeException( 'There was an error transferring the remote file.' );
        }

        return true;
    }
}
