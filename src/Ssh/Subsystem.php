<?php

namespace Ssh;

use RuntimeException;

/**
 * Abstract class for the SSH subsystems as Sftp and Publickey
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
abstract class Subsystem extends AbstractResourceProvider
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Returns the SSH session resource
     *
     * @return resource
     */
    protected function getSessionResource()
    {
        return $this->session->getResource();
    }
}
