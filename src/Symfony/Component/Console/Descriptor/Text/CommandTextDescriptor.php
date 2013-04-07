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

use Symfony\Component\Console\Command\Command;

/**
 * Command descriptor in text format.
 *
 * @author Loïc Chardonnet <loic.chardonnet@sensiolabs.com>
 */
class CommandTextDescriptor extends AbstractTextDescriptor
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
        $definitionDescriptor = new InputDefinitionTextDescriptor();

        if ($object->getApplication() && !$object->isApplicationDefinitionMerged()) {
            $object->getSynopsis();
            $object->mergeApplicationDefinition(false);
        }

        $messages = array(
            $raw ? 'Usage:' : '<comment>Usage:</comment>',
            ' '.$object->getSynopsis(),
            '',
        );

        $aliases = $object->getAliases();
        if ($aliases && $raw) {
            $messages[] = 'Aliases: '.implode(', ', $aliases);
        }
        if ($aliases && !$raw) {
            $messages[] = '<comment>Aliases:</comment> <info>'.implode(', ', $aliases).'</info>';
        }

        $messages[] = $object->getNativeDefinition()->asText();

        $help = $object->getProcessedHelp();
        if ($help) {
            $messages[] = $raw ? 'Help:' : '<comment>Help:</comment>';
            $messages[] = ' '.str_replace(PHP_EOL, PHP_EOL.' ', $help);
            $messages[] = '';
        }

        return implode(PHP_EOL, $messages);
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
        return true;
    }
}
