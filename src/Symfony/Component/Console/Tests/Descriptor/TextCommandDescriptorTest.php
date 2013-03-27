<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Descriptor;

use Symfony\Component\Console\Descriptor\TextCommandDescriptor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TextCommandDescriptorTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = __DIR__.'/../Fixtures/';
        require_once self::$fixturesPath.'/TestCommand.php';
    }

    public function testSupportsCommands()
    {
        $descriptor = new TextCommandDescriptor();

        $this->assertTrue($descriptor->supports(new \TestCommand()));
    }

    public function testDoesNotSupportNonCommands()
    {
        $descriptor = new TextCommandDescriptor();

        $this->assertFalse($descriptor->supports(new \stdClass()));
    }

    public function testGetFormat()
    {
        $descriptor = new TextCommandDescriptor();

        $this->assertSame('txt', $descriptor->getFormat());
    }

    public function testDecribing()
    {
        $command = new \TestCommand();
        $command->setApplication(new Application());
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName()));

        $descriptor = new TextCommandDescriptor();

        $this->assertStringEqualsFile(self::$fixturesPath.'/command_astext.txt', $descriptor->describe($command), '->describe($command) returns a text representation of the command');
    }
}
