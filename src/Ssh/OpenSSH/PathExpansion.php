<?php

declare(strict_types=1);

namespace Ssh\OpenSSH;

use UnexpectedValueException;

use function getenv;
use function is_string;
use function preg_replace_callback;

trait PathExpansion
{
    /**
     * Replaces '~/' with users home path
     */
    private function expandPath(string $path): string
    {
        return preg_replace_callback(
            '#^~/#',
            function (): string {
                $home= getenv('HOME');

                if (!is_string($home) || $home === '') {
                    throw new UnexpectedValueException('Could not read HOME directory from environment');
                }

                return $home . '/';
            },
            $path
        );
    }
}
