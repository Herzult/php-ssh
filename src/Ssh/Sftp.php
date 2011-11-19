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
     * Indicates whether the specified distant file exists
     *
     * @param  string $filename The distant filename
     *
     * @return boolean
     */
    public function exists($filename)
    {
        return file_exists($this->getUrl($filename));
    }

    /**
     * Reads the content of the specified remote file
     *
     * @param  string $filename The remote filename
     *
     * @return string
     */
    public function read($filename)
    {
        return file_get_contents($this->getUrl($filename));
    }

    /**
     * Writes the given content to the specified remote file
     *
     * @param  string $filename The remote filename
     *
     * @return integer The number of bytes that were written into the file, or
     *                 FALSE on failure
     */
    public function write($filename, $content)
    {
        return file_put_contents($this->getUrl($filename), $content);
    }

    /**
     * Receive the specified distant file as the specified local file
     *
     * @param  string $distant The distant filename
     * @param  string $local   The local filename
     *
     * @return boolean TRUE on success, or FALSE on failure
     */
    public function receive($distant, $local)
    {
        return file_put_contents($local, $this->read($distant));
    }

    /**
     * Sends the specified local file as the specified remote file
     *
     * @param  string $local   The local filename
     * @param  string $distant The distant filename
     *
     * @return boolean TRUE on success, or FALSE on failure
     */
    public function send($local, $distant)
    {
        $this->write($distant, file_get_contents($local));
    }

    /**
     * Returns the URL of the specified file with the ssh2.sftp protocol. The
     * result URL is suitable for stream resource creation (e.g using fopen)
     *
     * @param  string $filename The distant filename
     *
     * @return string
     */
    public function getUrl($filename)
    {
        return sprintf('ssh2.sftp://%s/%s', $this->getResource(), $filename);
    }

    /**
     * Scan a URL for files and directories
     *
     * Unfortunately, using a (recursive) directory iterator is not possible
     * over SFTP: see https://bugs.php.net/bug.php?id=57378. Also, is_dir() is
     * unreliable and often returns false for valid directories. Therefore, I
     * use @scandir() instead.
     *
     * @param string $url
     * @return array
     */
    protected function scanUrl($url)
    {
        if (!$files = @scandir($url)) {
            return null;
        }

        return array_filter($files, function($file) {
            if ($file != '.' && $file != '..') {
                return true;
            }
        });
    }

    /**
     * List files and directories in a directory
     *
     * @param string $directory
     * @param boolean $includeSubdirectories
     * @return array
     */
    public function listDirectory($directory, $includeSubdirectories = true)
    {
        $url = $this->getUrl($directory);

        $contents = array();
        foreach ($this->scanUrl($url) as $file) {
            if (true === $includeSubdirectories
                && $subFiles = $this->scanUrl("$url/$file")) {
                foreach ($subFiles as $subFile) {
                    $contents[] = "$directory/$file/$subFile";
                }
            } else {
                $contents[] = "$directory/$file";
            }
        }

        return $contents;
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
