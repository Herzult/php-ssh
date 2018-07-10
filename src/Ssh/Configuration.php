<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

interface Configuration
{
    public function getHost(): string;
    public function getPort(): int;

    /**
     * Methods for ssh2_connect()
     *
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function getMethods(): array;

    /**
     * The callbacks for ssh2_connect
     *
     * @return callable[] $callbacks
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function getCallbacks(): array;

    /**
     * Returns an array of argument designed for the ssh2_connect function
     *
     * @see http://php.net/manual/en/function.ssh2-connect.php
     */
    public function asArguments(): array;

    /**
     * The identity to connect with
     */
    public function getIdentity(): ?string;
}
