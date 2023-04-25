<?php

namespace Ssh;

/**
 * Abstract class for the SSH subsystems as Sftp and Publickey
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
abstract class Subsystem extends AbstractResourceProvider
{
    public function __construct(protected readonly Session $session)
    {
    }

    protected function getSessionResource(): Resource
    {
        return $this->session->getResource();
    }
}
