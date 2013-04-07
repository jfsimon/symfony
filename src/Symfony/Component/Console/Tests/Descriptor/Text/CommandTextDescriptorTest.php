<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/*
namespace Symfony\Component\Console\Tests\Descriptor\Text;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Descriptor\Text\TextCommandDescriptor;

class TextCommandDescriptorTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = __DIR__.'/../../Fixtures';
        require_once self::$fixturesPath.'/TestCommand.php';
    }

    public function testFormattedDescription()
    {
        $command = new \TestCommand();
        $command->setApplication(new Application());
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName()));

        $descriptor = new TextCommandDescriptor();

        $this->assertStringEqualsFile(
                self::$fixturesPath.'/Descriptors/formatted_command.txt',
                $descriptor->describe($command),
                '->describe($command) returns a formatted description in text format'
        );
    }

    public function testRawDescription()
    {
        $command = new \TestCommand();
        $command->setApplication(new Application());
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName()));

        $descriptor = new TextCommandDescriptor();

        $this->assertStringEqualsFile(
                self::$fixturesPath.'/Descriptors/raw_command.txt',
                $descriptor->describe($command, true),
                '->describe($command) returns a formatted description in text format'
        );
    }

    public function testSupportCommand()
    {
        $descriptor = new TextCommandDescriptor();
        $this->assertTrue(
                $descriptor->supports(new \TestCommand()),
                '->supports($commands) checks if the command is supported'
        );
    }

    public function testDoesNotSupportNonCommand()
    {
        $descriptor = new TextCommandDescriptor();
        $this->assertFalse(
                $descriptor->supports(new \stdClass()),
                '->supports($object) checks if the object is not supported'
        );
    }

    public function testFormat()
    {
        $descriptor = new TextCommandDescriptor();
        $this->assertSame(
                'txt',
                $descriptor->getFormat(),
                '->getFormat() returns the format'
        );
    }

    public function testUseFormating()
    {
        $descriptor = new TextCommandDescriptor();
        $this->assertTrue(
                $descriptor->useFormatting(),
                '->useFormatting() checks if formatting is used'
        );
    }
}*/
