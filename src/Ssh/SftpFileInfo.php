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
    private string $uri;

    public readonly string $remotePath;

    public function __construct(private Sftp $sftp, string $path)
    {
        $this->remotePath = $path;
        $this->uri = $sftp->buildUrl($path);

        parent::__construct($this->uri);
    }

    public function isDir(): bool
    {
        return $this->sftp->isDir($this->remotePath);
    }
}
