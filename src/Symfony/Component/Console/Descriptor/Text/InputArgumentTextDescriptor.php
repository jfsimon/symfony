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

use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class InputArgumentTextDescriptor extends AbstractTextDescriptor
{
    /**
     * @var int
     */
    private $indentation;

    /**
     * @param int $indentation
     */
    public function __construct($indentation = 0)
    {
        $this->indentation = $indentation;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $options)
    {
        $this->indentation = $options['indentation'];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function describe($object, $raw = false)
    {
        /** @var InputOption $object */

        $default = '';
        if (null !== $object->getDefault() && (!is_array($object->getDefault()) || count($object->getDefault()))) {
            $default = sprintf(
                $raw ? ' (default: %s)' : ' <comment>(default: %s)</comment>',
                $this->formatDefaultValue($object->getDefault())
            );
        }

        $description = str_replace(
            PHP_EOL,
            PHP_EOL.str_repeat(' ', $this->indentation + 2),
            $object->getDescription()
        );

        return sprintf(
            $raw ? " %s %-{$this->indentation}s%s" : " <info>%s</info> %-{$this->indentation}s%s",
            $object->getName(),
            $description,
            $default
        );
    }

    /**
     * @param mixed $default
     *
     * @return string
     */
    private function formatDefaultValue($default)
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            return str_replace('\/', '/', json_encode($default));
        }

        return json_encode($default, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof InputArgument;
    }
}
