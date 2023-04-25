<?php

declare(strict_types=1);

namespace Ssh;

use RuntimeException;

use function array_map;

/**
 * Wrapper for the SSH publickey subsystem
 */
class Publickey extends Subsystem
{
    /**
     * Adds an authorized publickey
     */
    public function add(PublicKeyEntry $key, bool $overwrite = false): bool
    {
        return ssh2_publickey_add(
            $this->getResource()->resource,
            $key->algoName,
            $key->data,
            $overwrite,
            $key->getAttributes()
        );
    }

    /**
     * Lists the currently authorized publickeys
     *
     * @return iterable<PublicKeyEntry>
     */
    public function getList(): iterable
    {
        return array_map(
            static fn (array $item) => PublicKeyEntry::fromArray($item),
            ssh2_publickey_list($this->getResource()->resource)
        );
    }

    /**
     * Removes an authorized publickey
     *
     * @param  string $algoname The algorithm (e.g: ssh-dss, ssh-rsa)
     * @param  string $blob     The blob as binary data
     */
    public function remove(PublicKeyEntry $key): bool
    {
        return ssh2_publickey_remove($this->getResource()->resource, $key->algoName, $key->data);
    }

    protected function createResource(): Resource
    {
        $resource = ssh2_publickey_init($this->getSessionResource()->resource);

        if (!is_resource($resource)) {
            throw new RuntimeException('The initialization of the publickey subsystem failed.');
        }

        return new Resource($resource);
    }
}
