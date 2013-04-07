<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor\Text;

use Symfony\Component\Console\Descriptor\DescriptorInterface;

/**
 * @author Loic Chardonnet <loic.chardonnet@sensiolabs.com>
 */
abstract class AbstractTextDescriptor implements DescriptorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(array $options)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function describe($object, $raw = false)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'txt';
    }

    /**
     * {@inheritdoc}
     */
    public function useFormatting()
    {
        return true;
    }
}

