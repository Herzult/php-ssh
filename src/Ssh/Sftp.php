<?php declare(strict_types=1);

namespace Ssh;

use Generator;
use RuntimeException;
use function substr;

/**
 * Secure File Transfer Protocol
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Sftp extends Subsystem
{
    /**
     * Stats a symbolic link
     */
    public function lstat(string $path): array
    {
        return ssh2_sftp_lstat($this->getResource(), $path);
    }

    /**
     * Creates a directory
     */
    public function mkdir(string $dirname, int $mode = null, bool $recursive = false): bool
    {
        return ssh2_sftp_mkdir($this->getResource(), $dirname, $mode, $recursive);
    }

    /**
     * Returns the target of a symbolic link
     */
    public function readlink(string $link): string
    {
        return ssh2_sftp_readlink($this->getResource(), $link);
    }

    /**
     * Resolves the realpath of a provided path string
     */
    public function realpath(string $filename): string
    {
        // This function creates a not documented warning on failure.
        return @ssh2_sftp_realpath($this->getResource(), $filename);
    }

    /**
     * Renames a remote file
     */
    public function rename(string $from, string $to): bool
    {
        return ssh2_sftp_rename($this->getResource(), $from, $to);
    }

    /**
     * Removes a directory
     */
    public function rmdir(string $dirname): bool
    {
        return ssh2_sftp_rmdir($this->getResource(), $dirname);
    }

    /**
     * Stats a file on the remote filesystem
     */
    public function stat(string $path): array
    {
        // This function creates a undocumented warning on missing files.
        return @ssh2_sftp_stat($this->getResource(), $path);
    }

    /**
     * Creates a symlink
     */
    public function symlink(string $target, string $link): bool
    {
        return ssh2_sftp_symlink($this->getResource(), $target, $link);
    }

    /**
     * Deletes a file
     */
    public function unlink(string $filename): bool
    {
        return ssh2_sftp_unlink($this->getResource(), $filename);
    }

    /**
     * Indicates whether the specified distant file exists
     */
    public function exists(string $filename): bool
    {
        return file_exists($this->buildUrl($filename));
    }

    /**
     * Reads the content of the specified remote file.
     * Will return false if file does not exist.
     *
     * @todo Throw exception on failure
     * @return string|bool
     */
    public function read(string $filename)
    {
        // Suppress a warning, when file does not exist.
        return @file_get_contents($this->buildUrl($filename));
    }

    /**
     * Writes the given content to the specified remote file
     *
     * @todo Throw exception on failure
     * @return int|bool The number of bytes that were written into the file, or false on failure
     */
    public function write(string $filename, string $content)
    {
        return file_put_contents($this->buildUrl($filename), $content);
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
     */
    public function send(string $local, string $distant): bool
    {
        return ($this->write($distant, file_get_contents($local)) !== false);
    }

    /**
     * Returns the URL of the specified file with the ssh2.sftp protocol. The
     * result URL is suitable for stream resource creation (e.g using fopen)
     */
    public function buildUrl(string $filename): string
    {
        return 'ssh2.sftp://' . intval($this->getResource()) . '/' . $filename;
    }

    /**
     * {@inheritDoc}
     */
    protected function createResource()
    {
        $resource = ssh2_sftp($this->getSessionResource());

        if (!is_resource($resource)) {
            throw new RuntimeException('The initialization of the SFTP subsystem failed.');
        }

        $this->resource = $resource;
    }

    public function isDir(string $path): bool
    {
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        return $this->exists($path);
    }

    /**
     * Scans a directory
     *
     * Unfortunately, using a (recursive) directory iterator is not possible
     * over SFTP: see https://bugs.php.net/bug.php?id=57378.
     *
     * @return Generator
     */
    private function scanDirectory(string $directory): Generator
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
