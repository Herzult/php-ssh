<?php

declare(strict_types=1);

namespace Ssh\Authentication;

use function file_exists;

final readonly class KeyPair
{
    public string $publicKeyFile;

    public function __construct(
        public string $privateKeyFile,
        string|null $publicKeyFile = null,
    ) {
        $this->publicKeyFile = $publicKeyFile ?? ($privateKeyFile . '.pub');
    }

    public function exists(): bool
    {
        return file_exists($this->privateKeyFile)
            && file_exists($this->publicKeyFile);
    }
}
