<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Descriptor\DescriptorInterface;
use Symfony\Component\Console\Command\Command;

/**
 * @author Loic Chardonnet <loic.chardonnet@sensiolabs.com>
 */
class TextCommandDescriptor implements DescriptorInterface
{
    /**
     * {@inheritdoc}
     */
    public function describe($object, $raw = false)
    {
        // TODO:
        // The following code was used. We need to investigate to make sure we
        // don't need it anymore.
        //
        // $application = $object->getApplication();
        // if ($application && !$this->applicationDefinitionMerged) {
        //     $object->getSynopsis();
        //     $this->mergeApplicationDefinition(false);
        // }

        $messages = array(
            '<comment>Usage:</comment>',
            ' '.$object->getSynopsis(),
            '',
        );

        if ($aliases = $object->getAliases()) {
            $messages[] = '<comment>Aliases:</comment> <info>'.implode(', ', $aliases).'</info>';
        }

        $messages[] = $object->getNativeDefinition()->asText();

        if ($help = $object->getProcessedHelp()) {
            $messages[] = '<comment>Help:</comment>';
            $messages[] = ' '.str_replace("\n", "\n ", $help);
            $messages[] = '';
        }

        return implode("\n", $messages);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof Command;
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
        return false;
    }
}
