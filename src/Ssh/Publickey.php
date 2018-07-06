<?php

namespace Ssh;

use RuntimeException;

/**
 * Wrapper for the SSH publickey subsystem
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Publickey extends Subsystem
{
    /**
     * Adds an authorized publickey
     *
     * @param  string  $algoname   The algorithm (e.g: ssh-dss, ssh-rsa)
     * @param  string  $blob       The blob as binary data
     * @param  Boolean $overwrite  Whether to overwrite the key if it already
     *                             exist
     * @param  array   $attributes An associative array of attributes to assign
     *                             to the publickey. To mark an attribute as
     *                             mandatory, precede its name with an asterisk.
     *                             If the server is unable to support an
     *                             attribute marked mandatory, it will abort
     *                             the add process.
     */
    public function add(string $algoname, string $blob, bool $overwrite = false, array $attributes = []): bool
    {
        return ssh2_publickey_add($this->getResource(), $algoname, $blob, $overwrite, $attributes);
    }

    /**
     * Lists the currently authorized publickeys
     *
     * @return array A numerically indexed array of keys, each of which is an
     *               associative array containing: name, blob, and attrs
     *               elements.
     */
    public function getList(): iterable
    {
        return ssh2_publickey_list($this->getResource());
    }

    /**
     * Removes an authorized publickey
     *
     * @param  string $algoname The algorithm (e.g: ssh-dss, ssh-rsa)
     * @param  string $blob     The blob as binary data
     */
    public function remove(string $algoname, string $blob): bool
    {
        return ssh2_publickey_remove($this->getResource(), $algoname, $blob);
    }

    /**
     * {@inheritDoc}
     */
    protected function createResource()
    {
        $resource = ssh2_publickey_init($this->getSessionResource());

        if (!is_resource($resource)) {
            throw new RuntimeException('The initialization of the publickey subsystem failed.');
        }

        $this->resource = $resource;
    }
}
