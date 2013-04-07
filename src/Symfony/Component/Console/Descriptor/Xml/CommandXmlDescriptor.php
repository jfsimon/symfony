<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor\Xml;

use Symfony\Component\Console\Command\Command;

/**
 * Command descriptor in XML format.
 *
 * @author Loïc Chardonnet <loic.chardonnet@sensiolabs.com>
 */
class CommandXmlDescriptor implements DescriptorInterface
{
    /**
     * {@inheritdoc}
     */
    public function describe($object, $raw = false)
    {
        if ($object->getApplication() && !$object->isApplicationDefinitionMerged()) {
            $object->getSynopsis();
            $object->mergeApplicationDefinition(false);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->appendChild($commandXML = $dom->createElement('command'));
        $commandXML->setAttribute('id', $object->getName());
        $commandXML->setAttribute('name', $object->getName());

        $commandXML->appendChild($usageXML = $dom->createElement('usage'));
        $usageXML->appendChild($dom->createTextNode(sprintf($object->getSynopsis(), '')));

        $commandXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode(str_replace("\n", "\n ", $object->getDescription())));

        $commandXML->appendChild($helpXML = $dom->createElement('help'));
        $helpXML->appendChild($dom->createTextNode(str_replace("\n", "\n ", $object->getProcessedHelp())));

        $commandXML->appendChild($aliasesXML = $dom->createElement('aliases'));
        foreach ($object->getAliases() as $alias) {
            $aliasesXML->appendChild($aliasXML = $dom->createElement('alias'));
            $aliasXML->appendChild($dom->createTextNode($alias));
        }

        $definition = $object->getNativeDefinition()->asXml(true);
        $commandXML->appendChild($dom->importNode($definition->getElementsByTagName('arguments')->item(0), true));
        $commandXML->appendChild($dom->importNode($definition->getElementsByTagName('options')->item(0), true));

        return $raw ? $dom : $dom->saveXml();
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
        return 'xml';
    }

    /**
     * {@inheritdoc}
     */
    public function useFormatting()
    {
        return false;
    }
}
