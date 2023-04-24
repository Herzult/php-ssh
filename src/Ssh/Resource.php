<?php

declare(strict_types=1);

namespace Ssh;

use InvalidArgumentException;

use function gettype;
use function is_resource;

final readonly class Resource
{
    /**
     * @var resource
     */
    public mixed $resource;

    public function __construct(mixed $resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid SSH resource: ' . gettype($resource));
        }

        $this->resource = $resource;
    }
}
