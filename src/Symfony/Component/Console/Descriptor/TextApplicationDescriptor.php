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

use Symfony\Component\Console\Application;

/**
 * Application descriptor in text format.
 *
 * @author Loïc Chardonnet <loic.chardonnet@sensiolabs.com>
 */
class TextApplicationDescriptor implements DescriptorInterface
{
    /**
     * {@inheritdoc}
     */
    public function describe($object, $raw = false)
    {
        // @todo: manage namespace like Application->asText().
        // $commands = $namespace ? $object->all($object->findNamespace($namespace)) : $object->all();
        $commands = $object->all();

        $width = 0;
        foreach ($commands as $command) {
            $width = strlen($command->getName()) > $width ? strlen($command->getName()) : $width;
        }
        $width += 2;

        $messages = array();
        if (!$raw) {
            $messages[] = $object->getHelp();
            $messages[] = '';
            $messages[] = sprintf('<comment>Available commands:</comment>');
            // @todo: manage namespace like Application->asText().
            // $namespace ? "for the \"$namespace\" namespace" : ''
        }

        foreach ($this->sortCommands($commands) as $space => $commands) {
            // @todo: manage namespace like Application->asText().
            // && !$namespace
            if (!$raw && '_global' !== $space) {
                $messages[] = '<comment>'.$space.'</comment>';
            }

            foreach ($commands as $name => $command) {
                $messages[] = sprintf(
                        $raw ? "%-${width}s %s" : "  <info>%-${width}s</info> %s",
                        $name,
                        $command->getDescription()
                );
            }
        }

        return implode(PHP_EOL, $messages);
    }

    /**
     * Sorts commands in alphabetical order.
     *
     * @param array $commands An associative array of commands to sort
     *
     * @return array A sorted array of commands
     */
    private function sortCommands($commands)
    {
        $namespacedCommands = array();
        foreach ($commands as $name => $command) {
            $key = $this->extractNamespace($name, 1);
            if (!$key) {
                $key = '_global';
            }

            $namespacedCommands[$key][$name] = $command;
        }
        ksort($namespacedCommands);

        foreach ($namespacedCommands as &$commands) {
            ksort($commands);
        }

        return $namespacedCommands;
    }

    /**
     * Returns the namespace part of the command name.
     *
     * @param string $name  The full name of the command
     * @param string $limit The maximum number of parts of the namespace
     *
     * @return string The namespace of the command
     */
    private function extractNamespace($name, $limit = null)
    {
        $parts = explode(':', $name);
        array_pop($parts);

        return implode(':', null === $limit ? $parts : array_slice($parts, 0, $limit));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof Application;
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
