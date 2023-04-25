<?php

declare(strict_types=1);

namespace Ssh\OpenSSH;

use RuntimeException;

use function array_map;
use function explode;
use function file;
use function str_starts_with;
use function strtolower;

final class Parser
{
    /**
     * Parses the ssh config file into an array of configs for later matching against hosts
     *
     * @return array<string, array<string, string>>
     */
    public function parse(string $file): array
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new RuntimeException("The file '$file' does not exist or is not readable");
        }

        $hosts = ['*'];
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $configs = [];

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);

            if ($line == '' || str_starts_with($line, '#')) {
                continue;
            }

            $delimiter = (str_contains($line, '='))? '=' : ' ';
            $pair = explode($delimiter, $line, 2);

            if (count($pair) !== 2) {
                throw new RuntimeException(sprintf('The file "%s" is not parsable at line %d', $file, $lineNumber + 1));
            }

            list($key, $value) = array_map('trim', $pair);
            $key = strtolower($key);

            if ($key == 'host') {
                $hosts = array_map('trim', explode(' ', $value));
            }

            foreach ($hosts as $host) {
                $configs[$host][$key] = $value;
            }
        }

        return $configs;
    }
}
