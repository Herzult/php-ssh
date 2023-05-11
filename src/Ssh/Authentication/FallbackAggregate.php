<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2023 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh\Authentication;

use Ssh\Authentication;
use Ssh\Session;

use function array_values;

final readonly class FallbackAggregate implements Authentication
{
    /**
     * @var non-empty-list<Authentication>
     */
    private array $options;

    public function __construct(Authentication $primary, Authentication ...$fallbacks)
    {
        $this->options = [
            $primary,
            ...array_values($fallbacks),
        ];
    }

    public static function aggregate(Authentication $authentication, Authentication|null $aggregateTo = null): self
    {
        if ($aggregateTo === null || $aggregateTo instanceof None) {
            return new self($authentication);
        }

        $aggregateTo = $aggregateTo instanceof self ? $aggregateTo : new self($aggregateTo);

        return $aggregateTo->with($authentication);
    }

    public function with(Authentication $fallback): self
    {
        return new self(...$this->options, $fallback);
    }

    /**
     * @inheritDoc
     */
    function authenticate(Session $session): bool
    {
        foreach ($this->options as $option) {
            if ($option->authenticate($session)) {
                return true;
            }
        }

        return false;
    }
}
