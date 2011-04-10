<?php

namespace Ssh;

use RuntimeException;

/**
 * Secure File Transfer Protocol
 *
 * @author Antoine Hérault <antoine.herault@gmail.com>
 */
class Sftp extends Subsystem
{
    /**
     * Stats a symbolic link
     *
     * @param  string $path The path of the symbolic link
     *
     * @return array
     */
    public function lstat($path)
    {
        return ssh2_sftp_lstat($this->getResource(), $path);
    }

    /**
     * Creates a directory
     *
     * @param  string  $dirname   The name of the new directory
     * @param  int     $mod       The permissions on the new directory
     * @param  Boolean $recursive Whether to automatically create any required
     *                            parent directory
     *
     * @return Boolean
     */
    public function mkdir($dirname, $mod = 0777, $recursive = false)
    {
        return ssh2_sftp_mkdir($this->getResource(), $dirname, $mod, $recursive);
    }

    /**
     * Returns the target of a symbolic link
     *
     * @param  string $link The path of the symbolic link
     *
     * @return string The target of the symbolic link
     */
    public function readlink($link)
    {
        return ssh2_sftp_readlink($this->getResource(), $link);
    }

    /**
     * Resolves the realpath of a provided path string
     *
     * @param  string $filename The filename to resolve
     *
     * @return string The real path of the file
     */
    public function realpath($filename)
    {
        return ssh2_sftp_realpath($this->getResource(), $filename);
    }

    /**
     * Renames a remote file
     *
     * @param  string $from The current file that is being renamed
     * @param  string $to   The new file name that replaces from
     *
     * @return Boolean TRUE on success, or FALSE on failure
     */
    public function rename($from, $to)
    {
        return ssh2_sftp_rename($this->getResource(), $from, $to);
    }

    /**
     * Removes a directory
     *
     * @param  string $dirname The directory that is being removed
     *
     * @return Boolean TRUE on success, or FALSE on failure
     */
    public function rmdir($dirname)
    {
        return ssh2_sftp_rmdir($this->getResource(), $dirname);
    }

    /**
     * Stats a file on the remote filesystem
     *
     * @param  string $path The path of the file
     *
     * @return array
     */
    public function stat($path)
    {
        return ssh2_sftp_stat($this->getResource(), $path);
    }

    /**
     * Creates a symlink
     *
     * @param  string $target The target of the symlink
     * @param  string $link   The path of the link
     *
     * @return Boolean TRUE on success, or FALSE on failure
     */
    public function symlink($target, $link)
    {
        return ssh2_sftp_symlink($this->getResource(), $symlink, $link);
    }

    /**
     * Deletes a file
     *
     * @param  string $filename The name of the file that is being deleted
     *
     * @return Boolean TRUE on success, or FALSE on failure
     */
    public function unlink($filename)
    {
        return ssh2_sftp_symlink($this->getResource(), $filename);
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
}
