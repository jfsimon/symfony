<?php

namespace Symfony\Component\Finder\Filter;

use Symfony\Component\Finder\Expression\Expression;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class ExcludeFilter implements FilterInterface
{
    /**
     * @var string[]
     */
    private $patterns = array();

    /**
     * @param array $paths
     */
    public function __construct(array $paths)
    {
        $this->patterns = array_map(function ($path) {
            return '#(^|/)'.preg_quote($path, '#').'(/|$)#';
        }, $paths);
    }

    /**
     * {@inheritdoc}
     */
    public function reject(SplFileInfo $file)
    {
        $relativePath = $file->isDir() ? $file->getRelativePathname() : $file->getRelativePath();

        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, strtr($relativePath, '\\', '/'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->patterns);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_TREE;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 40;
    }
}
