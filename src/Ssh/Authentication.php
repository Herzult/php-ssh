<?php declare(strict_types=1);

namespace Ssh;

/**
 * Interface that must be implemented by the authentication classes
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
interface Authentication
{
    /**
     * Authenticates the given SSH session
     */
    function authenticate(Session $session): bool;
}
