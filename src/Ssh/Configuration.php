<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

/**
 * @psalm-type SSHMACArray = array{crypt?: string, comp?: string, mac?: string}
 * @psalm-type SSHCallbacksArray = array{
 *     ignore?: callable(string):void,
 *     debug?: callable(string, mixed, mixed):void,
 *     macerror?: callable(mixed):bool,
 *     disconnect?: callable(mixed, string, mixed):void,
 *  }
 * @psalm-type SSHMethodsArray = array{kex?: string[], hostkey?: string, client_to_server?: SSHMACArray, server_to_client?: SSHMACArray}
 */
interface Configuration
{
    public function getHost(): string;
    public function getPort(): int;

    /**
     * Methods for ssh2_connect()
     *
     * @return SSHMethodsArray
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function getMethods(): array;

    /**
     * The callbacks for ssh2_connect
     *
     * @return SSHCallbacksArray
     * @see http://php.net/manual/en/function.ssh2-connect.php ssh2_connect
     */
    public function getCallbacks(): array;

    /**
     * Returns an array of argument designed for the ssh2_connect function
     *
     * @return array{host: string, port?: int, methods?: SSHMethodsArray, callbacks?: SSHCallbacksArray}
     * @see http://php.net/manual/en/function.ssh2-connect.php
     */
    public function asArguments(): array;

    /**
     * The identity to connect with
     */
    public function getIdentity(): string | null;
}
