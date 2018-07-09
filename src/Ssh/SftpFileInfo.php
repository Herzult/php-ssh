<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use SplFileInfo;

final class SftpFileInfo extends SplFileInfo
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $remotePath;

    /**
     * @var Sftp
     */
    private $sftp;

    public function __construct(Sftp $sftp, string $path)
    {
        $this->remotePath = $path;
        $this->uri = $sftp->buildUrl($path);
        $this->sftp = $sftp;

        parent::__construct($this->uri);
    }

    public function getRemotePath(): string
    {
        return $this->remotePath;
    }

    public function isDir(): bool
    {
        return $this->sftp->isDir($this->remotePath);
    }
}
