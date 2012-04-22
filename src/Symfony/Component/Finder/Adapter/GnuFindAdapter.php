<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Adapter;

use Symfony\Component\Finder\Iterator;
use Symfony\Component\Finder\ShellTester;

/**
 * PHP finder engine implementation.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class GnuFindAdapter extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    public function searchInDirectory($dir)
    {
        // -noleaf option is required for filesystems
        // who doesn't follow '.' and '..' convention
        // like MSDOS, CDROM or AFS mount points
        $command = 'find '.$dir.' -noleaf';

        if ($this->followLinks) {
            $command.= ' -follow';
        }

        $command.= ' -mindepth '.($this->minDepth+1);

        // warning! INF < INF => true ; INF == INF => false ; INF === INF => true
        // https://bugs.php.net/bug.php?id=9118
        if ($this->maxDepth !== INF) {
            $command.= ' -maxdepth '.($this->maxDepth+1);
        }

        if (Iterator\FileTypeFilterIterator::ONLY_DIRECTORIES === $this->mode) {
            $command.= ' -type d';
        } elseif (Iterator\FileTypeFilterIterator::ONLY_FILES === $this->mode) {
            $command.= ' -type f';
        }

        exec($command, $paths, $code);

        if ($code !== 0) {
            throw new \RuntimeException();
        }

        $iterator = new Iterator\FilePathsIterator($paths, $dir);

        if ($this->exclude) {
            $iterator = new Iterator\ExcludeDirectoryFilterIterator($iterator, $this->exclude);
        }

        if ($this->names || $this->notNames) {
            $iterator = new Iterator\FilenameFilterIterator($iterator, $this->names, $this->notNames);
        }

        if ($this->contains || $this->notContains) {
            $iterator = new Iterator\FilecontentFilterIterator($iterator, $this->contains, $this->notContains);
        }

        if ($this->sizes) {
            $iterator = new Iterator\SizeRangeFilterIterator($iterator, $this->sizes);
        }

        if ($this->dates) {
            $iterator = new Iterator\DateRangeFilterIterator($iterator, $this->dates);
        }

        if ($this->filters) {
            $iterator = new Iterator\CustomFilterIterator($iterator, $this->filters);
        }

        if ($this->sort) {
            $iteratorAggregate = new Iterator\SortableIterator($iterator, $this->sort);
            $iterator = $iteratorAggregate->getIterator();
        }

        return $iterator;
    }
}
