<?php
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   LUKA Proprietary
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace Ssh;

use Generator;
use LogicException;
use RecursiveIterator;

final class SftpDirectoryIterator implements RecursiveIterator
{
    /**
     * @var Generator
     */
    private $items;
    /**
     * @var Sftp
     */
    private $sftp;
    /**
     * @var string
     */
    private $dirname;

    public function __construct(Sftp $sftp, string $dirname)
    {
        if (!$sftp->isDir($dirname)) {
            throw new LogicException(sprintf('"%s" is no directory', $dirname));
        }

        $this->sftp = $sftp;
        $this->dirname = $dirname;
        $this->items = $sftp->scanDirectory($dirname);
    }

    public function current(): SftpFileInfo
    {
        return new SftpFileInfo($this->sftp, $this->items->current());
    }

    public function next(): void
    {
        $this->items->next();
    }

    public function key(): string
    {
        return $this->items->current();
    }

    public function valid(): bool
    {
        return $this->items->valid();
    }

    public function rewind(): void
    {
        $this->items->rewind();
    }

    public function hasChildren(): bool
    {
        return $this->valid() && $this->sftp->isDir($this->items->current());
    }

    public function getChildren(): self
    {
        if (!$this->hasChildren()) {
            throw new LogicException('The current item does not provide children');
        }

        return new self($this->sftp, $this->items->current());
    }
}
