<?php

namespace Symfony\Component\Console\Descriptor;

/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class DescriptorStack
{
    /**
     * @var DescriptorInterface[]
     */
    private $descriptors;

    /**
     * Adds a descriptor to the stack.
     *
     * @param DescriptorInterface $descriptor
     *
     * @return DescriptorStack
     */
    public function add(DescriptorInterface $descriptor)
    {
        $this->descriptors[] = $descriptor;

        return $this;
    }

    /**
     * Tries to describe given object in given format.
     *
     * @param mixed  $object Object to describe
     * @param string $format Description format
     *
     * @return string Formatted description
     *
     * @throws \InvalidArgumentException If no descriptors was found
     */
    public function describe($object, $format)
    {
        foreach ($this->descriptors as $descriptor) {
            if ($format === $descriptor->getFormat() && $descriptor->supports($object)) {
                return $descriptor->describe($object);
            }
        }

        throw new \InvalidArgumentException();
    }
}
