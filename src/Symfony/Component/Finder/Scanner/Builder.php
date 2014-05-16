<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Scanner;

use Symfony\Component\Finder\Comparator\Comparator;

/**
 * Scanner constraints builder.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class Builder
{
    private $type;
    private $minDepth;
    private $maxDepth;
    private $excludedNames;

    private $filenameConstraints = array();
    private $pathnameConstraints = array();

    /**
     * Constructor.
     *
     * @param int          $type
     * @param Comparator[] $depths
     * @param array        $excludedNames
     */
    public function __construct($type = Constraints::TYPE_ALL, array $depths, array $excludedNames = array())
    {
        $this->type = $type;
        $this->computeDepths($depths);
        $this->excludedNames = array_merge(array('.', '..'), $excludedNames);
    }

    /**
     * Adds name exclusion constraint.
     *
     * @param Expression $expression
     *
     * @return Builder
     */
    public function notName(Expression $expression)
    {
        $key = $expression->isRegex() ? 'excluded_patterns' : 'excluded_filenames';

        if (!isset($this->filenameConstraints[$key])) {
            $this->filenameConstraints[$key] = array();
        }

        $this->filenameConstraints[$key][] = $expression->getValue();

        return $this;
    }

    /**
     * Adds path exclusion constraint.
     *
     * @param Expression $expression
     *
     * @return Builder
     */
    public function notPath(Expression $expression)
    {
        $regex = $expression->getRegex(true);
        $key = $regex->isEnding() ? 'excluded_ending_patterns' : 'excluded_patterns';

        if (!isset($this->pathnameConstraints[$key])) {
            $this->pathnameConstraints[$key] = array();
        }

        $this->pathnameConstraints[$key][] = $regex;

        return $this;
    }

    /**
     * Adds name matching constraint.
     *
     * @param Expression $expression
     *
     * @return Builder
     */
    public function name(Expression $expression)
    {
        $key = $expression->isRegex() ? 'included_patterns' : 'included_filenames';

        if (!isset($this->filenameConstraints[$key])) {
            $this->filenameConstraints[$key] = array();
        }

        $this->filenameConstraints[$key][] = $expression->getValue();

        return $this;
    }

    /**
     * Adds path matching constraint.
     *
     * @param Expression $expression
     *
     * @return Builder
     */
    public function path(Expression $expression)
    {
        $regex = $expression->getRegex(true);
        $key = $regex->isEnding() ? 'included_ending_patterns' : 'included_patterns';

        if (!isset($this->pathnameConstraints[$key])) {
            $this->pathnameConstraints[$key] = array();
        }

        $this->pathnameConstraints[$key][] = $regex;

        return $this;
    }

    /**
     * Builds constraints.
     *
     * @return Constraints
     */
    public function build()
    {
        foreach ($this->pathnameConstraints as $key => $regexs) {
            $this->pathnameConstraints[$key] = array_map(function (Regex $regex) { return (string) $regex; }, Regex::mergeWithOr($regexs));
        }

        foreach ($this->filenameConstraints as $key => $regexs) {
            if (false !== strpos($key, 'patterns')) {
                $this->filenameConstraints[$key] = array_map(function (Regex $regex) { return (string) $regex; }, Regex::mergeWithOr($regexs));
            }
        }

        return new Constraints(
            $this->type, $this->minDepth, $this->maxDepth,
            $this->excludedNames, $this->pathnameConstraints, $this->filenameConstraints
        );
    }

    /**
     * Computes min/max depths.
     *
     * @param Comparator[] $depths
     */
    private function computeDepths(array $depths)
    {
        $this->minDepth = 0;
        $this->maxDepth = PHP_INT_MAX;

        foreach ($depths as $comparator) {
            switch ($comparator->getOperator()) {
                case '>':
                    $this->minDepth = (int) $comparator->getTarget() + 1;
                    break;
                case '>=':
                    $this->minDepth = (int) $comparator->getTarget();
                    break;
                case '<':
                    $this->maxDepth = (int) $comparator->getTarget() - 1;
                    break;
                case '<=':
                    $this->maxDepth = (int) $comparator->getTarget();
                    break;
                default:
                    $this->minDepth = $this->maxDepth = (int) $comparator->getTarget();
            }
        }
    }
}
