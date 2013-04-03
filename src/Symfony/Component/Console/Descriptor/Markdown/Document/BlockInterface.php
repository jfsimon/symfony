<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor\Markdown\Document;

/**
 * Document block interface.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
interface BlockInterface
{
    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * Formats block output.
     *
     * @param Formatter $formatter
     *
     * @return string
     */
    public function format(Formatter $formatter);
}
