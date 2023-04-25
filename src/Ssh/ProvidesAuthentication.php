<?php

declare(strict_types=1);

namespace Ssh;

interface ProvidesAuthentication
{
    public function createAuthentication(string|null $passphrase = null, string|null $user = null): Authentication;
}
