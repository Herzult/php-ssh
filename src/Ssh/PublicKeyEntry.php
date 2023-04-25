<?php

declare(strict_types=1);

namespace Ssh;

use function assert;
use function is_array;
use function is_string;

final class PublicKeyEntry
{
    /**
     * @param string $algoName
     * @param string $data
     * @param array<string, string> $attributes
     */
    public function __construct(
        public readonly string $algoName,
        public readonly string $data,
        private array $attributes = [],
    ) {
    }

    public static function fromArray(array $item): self
    {
        assert(
            isset($item['name']) && is_string($item['name'])
            && isset($item['blob']) && is_string($item['blob'])
            && (!isset($item['attrs']) || is_array($item['attrs']))
        );

        return new self($item['name'], $item['blob'], $item['attrs']);
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return $this
     */
    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function removeAttribute(string $name): self
    {
        unset($this->attributes[$name]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clearAttributes(): self
    {
        $this->attributes = [];
        return $this;
    }
}
