<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2023 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\Authentication;

use ArrayIterator;
use CallbackFilterIterator;
use Countable;
use IteratorAggregate;
use Traversable;

use function array_values;
use function getenv;
use function is_string;
use function iterator_count;

/**
 * @implements IteratorAggregate<KeyPair>
 */
final readonly class KeyPairOptions implements IteratorAggregate, Countable
{
    /**
     * @var list<KeyPair>
     */
    public array $options;

    public function __construct(KeyPair ...$options)
    {
        $this->options = array_values($options);
    }

    public static function fromFilename(string|null $file): self
    {
        return self::fromKeyPair($file !== null ? new KeyPair($file) : null);
    }

    public static function fromKeyPair(KeyPair|null $keyPair): self
    {
        if ($keyPair !== null) {
            return new self($keyPair);
        }

        $home = getenv('HOME');
        $path = is_string($home) ? $home : '';
        $path .= '/.ssh';

        return new self(
            new KeyPair($path . '/id_rsa'),
            new KeyPair($path . '/id_ecdsa'),
            new KeyPair($path . '/id_dsa'),
        );
    }

    /**
     * @return Traversable<KeyPair>
     */
    public function getIterator(): Traversable
    {
        return new CallbackFilterIterator(
            new ArrayIterator($this->options),
            static fn (KeyPair $current) => $current->exists(),
        );
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
}
