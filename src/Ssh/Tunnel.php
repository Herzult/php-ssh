<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use Ssh\Exception\IOException;

use function is_resource;

class Tunnel extends Subsystem
{
    protected function createResource(): Resource
    {
        return $this->getSessionResource();
    }

    /**
     * @return resource The tunneled socket stream
     */
    public function create(string $host, int $port): mixed
    {
        $tunnel = ssh2_tunnel($this->getResource()->resource, $host, $port);

        if (!is_resource($tunnel)) {
            throw IOException::tunnelError($host, $port);
        }

        return $tunnel;
    }
}
