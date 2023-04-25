<?php

declare(strict_types=1);

namespace Ssh;

use Generator;
use RuntimeException;
use Ssh\Exception\IOException;

use function ltrim;

/**
 * Secure File Transfer Protocol
 */
class Sftp extends Subsystem
{
    /**
     * Stats a symbolic link
     */
    public function lstat(string $path): array
    {
        return ssh2_sftp_lstat($this->getResource()->resource, $path);
    }

    /**
     * Creates a directory
     */
    public function mkdir(string $dirname, int $mode = null, bool $recursive = false): bool
    {
        return ssh2_sftp_mkdir($this->getResource()->resource, $dirname, $mode, $recursive);
    }

    /**
     * Returns the target of a symbolic link
     */
    public function readlink(string $link): string
    {
        return ssh2_sftp_readlink($this->getResource()->resource, $link);
    }

    /**
     * Resolves the realpath of a provided path string
     */
    public function realpath(string $filename): ?string
    {
        // This function creates a not documented warning on failure.
        $result = @ssh2_sftp_realpath($this->getResource()->resource, $filename);
        return $result? : null;
    }

    /**
     * Renames a remote file
     */
    public function rename(string $from, string $to): bool
    {
        return ssh2_sftp_rename($this->getResource()->resource, $from, $to);
    }

    /**
     * Removes a directory
     */
    public function rmdir(string $dirname): bool
    {
        return ssh2_sftp_rmdir($this->getResource()->resource, $dirname);
    }

    /**
     * Stats a file on the remote filesystem
     */
    public function stat(string $path): array
    {
        // This function creates a undocumented warning on missing files.
        return @ssh2_sftp_stat($this->getResource()->resource, $path);
    }

    /**
     * Creates a symlink
     */
    public function symlink(string $target, string $link): bool
    {
        return ssh2_sftp_symlink($this->getResource()->resource, $target, $link);
    }

    /**
     * Deletes a file
     */
    public function unlink(string $filename): bool
    {
        return ssh2_sftp_unlink($this->getResource()->resource, $filename);
    }

    /**
     * Indicates whether the specified distant file exists
     */
    public function exists(string $filename): bool
    {
        $url = $this->buildUrl($filename);
        $stat = @stat($url);

        return ($stat !== false);
    }

    /**
     * Reads the content of the specified remote file.
     * Will return false if file does not exist.
     *
     * @throws RuntimeException
     * @return string
     */
    public function read(string $filename): string
    {
        // Suppress a warning, when file does not exist.
        $url = $this->buildUrl($filename);
        $data = @file_get_contents($this->buildUrl($filename));

        if ($data === false) {
            throw IOException::readError(
                $filename,
                $this->session->configuration->getHost()
            );
        }

        return $data;
    }

    /**
     * Writes the given content to the specified remote file
     *
     * @throws IOException
     * @return int The number of bytes that were written into the file
     */
    public function write(string $filename, string $content): int
    {
        $bytes = file_put_contents($this->buildUrl($filename), $content);

        if ($bytes === false) {
            throw IOException::writeError(
                $filename,
                $this->session->configuration->getHost()
            );
        }

        return $bytes;
    }

    /**
     * Receive the specified distant file as the specified local file
     */
    public function receive(string $distant, string $local): bool
    {
        return (file_put_contents($local, $this->read($distant)) !== false);
    }

    /**
     * Sends the specified local file as the specified remote file
     *
     * @throws IOException
     */
    public function send(string $local, string $distant): void
    {
        $this->write($distant, file_get_contents($local));
    }

    /**
     * Returns the URL of the specified file with the ssh2.sftp protocol. The
     * result URL is suitable for stream resource creation (e.g using fopen)
     */
    public function buildUrl(string $filename): string
    {
        return 'ssh2.sftp://' . intval($this->getResource()->resource) . '/' . ltrim($filename, '/');
    }

    protected function createResource(): Resource
    {
        $resource = ssh2_sftp($this->getSessionResource()->resource);

        if (!is_resource($resource)) {
            throw new RuntimeException('The initialization of the SFTP subsystem failed.');
        }

        return new Resource($resource);
    }

    public function isDir(string $path): bool
    {
        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }

        return $this->exists($path);
    }

    public function getDirectory(string $path): SftpDirectoryIterator
    {
        return new SftpDirectoryIterator($this, $path);
    }

    /**
     * Scans a directory
     *
     * Unfortunately, using a (recursive) directory iterator is not possible
     * over SFTP: see https://bugs.php.net/bug.php?id=57378.
     *
     * @return Generator
     */
    public function scanDirectory(string $directory): Generator
    {
        if (!$results = @scandir($this->buildUrl($directory))) {
            return;
        }

        foreach ($results as $result) {
            if (in_array($result, ['.', '..'])) {
                continue;
            }

            $filename = sprintf('%s/%s', $directory, $result);

            if ($this->isDir($filename)) {
                $filename .= '/'; // State clearly that this is a directory
            }

            yield $filename;
        }
    }
}
