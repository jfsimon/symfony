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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Descriptor\ApplicationDescription;

/**
 * Application descriptor in text format.
 *
 * @author Loïc Chardonnet <loic.chardonnet@sensiolabs.com>
 */
class TextApplicationDescriptor extends AbstractTextDescriptor
{
    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @param string|null $namespace
     */
    public function __construct($namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $options)
    {
        $this->namespace = $options['namespace'];

        return parent::configure($options);
    }

    /**
     * {@inheritdoc}
     */
    public function describe($object, $raw = false)
    {
        $description = new ApplicationDescription($object, $this->namespace);
        $descriptor = new CommandTextDescriptor();

        $commands = $description->getCommands();

        $width = 0;
        foreach ($commands as $command) {
            $width = strlen($command->getName()) > $width ? strlen($command->getName()) : $width;
        }
        $width += 2;

        $messages = array();
        if (!$raw) {
            $messages[] = $object->getHelp();
            $messages[] = '';
            $messages[] = sprintf(
                '<comment>Available commands%s:</comment>',
                $this->namespace ? '' : " for the \"{$this->namespace}\"namespace"
            );
        }

        foreach ($description->getNamespaces() as $namespace) {
            if (!$raw && ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                $messages[] = '<comment>'.$namespace['id'].'</comment>';
            }

            foreach ($namespace['commands'] as $commandName) {
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
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof Application;
    }
}

