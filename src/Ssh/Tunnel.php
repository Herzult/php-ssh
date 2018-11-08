<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use function is_resource;
use RuntimeException;
use Ssh\Exception\IOException;
use function ssh2_tunnel;

final class Tunnel extends Subsystem
{
    /**
     * Creates the underlying resource
     *
     * @throws RuntimeException on resource creation failure
     */
    protected function createResource()
    {
        $this->resource = $this->getSessionResource();
    }

    /**
     * @return resource The tunneled socket stream
     */
    public function create(string $host, int $port)
    {
        $tunnel = ssh2_tunnel($this->getResource(), $host, $port);

        if (!is_resource($tunnel)) {
            throw IOException::tunnelError($host, $port);
        }

        return $tunnel;
    }
}
