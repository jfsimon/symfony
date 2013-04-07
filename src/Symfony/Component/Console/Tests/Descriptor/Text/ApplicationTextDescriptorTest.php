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
use Symfony\Component\Console\Descriptor\Text\TextApplicationDescriptor;

class TextApplicationDescriptorTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = __DIR__.'/../../Fixtures';
        require_once self::$fixturesPath.'/TestCommand.php';
    }

    protected function normalizeLineBreaks($text)
    {
        return str_replace(PHP_EOL, "\n", $text);
    }*/

    /**
     * Replaces the dynamic placeholders of the command help text with a static version.
     * The placeholder %command.full_name% includes the script path that is not predictable
     * and can not be tested against.
     *//*
    protected function ensureStaticCommandHelp(Application $application)
    {
        foreach ($application->all() as $command) {
            $command->setHelp(str_replace(
                    '%command.full_name%',
                    'app/console %command.name%',
                    $command->getHelp()
            ));
        }
    }

    public function testFormattedDescription()
    {
        $application = new Application();
        $application->add(new \FooCommand);
        $this->ensureStaticCommandHelp($application);

        $descriptor = new TextApplicationDescriptor();

        $this->assertStringEqualsFile(
                self::$fixturesPath.'/Descriptors/formatted_application.txt',
                $this->normalizeLineBreaks($descriptor->describe($application)),
                '->describe($application) returns a formatted description in text format'
        );
    }

    public function testRawDescription()
    {
        $application = new Application();
        $application->add(new \FooCommand);
        $this->ensureStaticCommandHelp($application);

        $descriptor = new TextApplicationDescriptor();

        $this->assertStringEqualsFile(
                self::$fixturesPath.'/Descriptors/raw_application.txt',
                $this->normalizeLineBreaks($descriptor->describe($application, true)),
                '->describe($application, true) returns a raw description in text format'
        );
    }

    public function testSupportApplication()
    {
        $descriptor = new TextApplicationDescriptor();
        $this->assertTrue(
                $descriptor->supports(new Application()),
                '->supports($application) checks if the application is supported'
        );
    }

    public function testDoesNotSupportNonApplication()
    {
        $descriptor = new TextApplicationDescriptor();
        $this->assertFalse(
                $descriptor->supports(new \stdClass()),
                '->supports($object) checks if the object is not supported'
        );
    }

    public function testFormat()
    {
        $descriptor = new TextApplicationDescriptor();
        $this->assertSame(
                'txt',
                $descriptor->getFormat(),
                '->getFormat() returns the format'
        );
    }

    public function testUseFormating()
    {
        $descriptor = new TextApplicationDescriptor();
        $this->assertTrue(
                $descriptor->useFormatting(),
                '->useFormatting() checks if formatting is used'
        );
    }
}*/
