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
class XmlCommandDescriptor implements DescriptorInterface
{
    private $asDom = false;

    /**
     * Whether to return a DOM or an XML string.
     *
     * @param boolean $asDom Whether to return a DOM or an XML string
     */
    public function setAsDom($asDom)
    {
        $this->asDom = $asDom;
    }

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

        return $this->asDom ? $dom : $dom->saveXml();
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
