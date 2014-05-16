<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests;

use Symfony\Component\Finder\Finder;

class FinderTest extends Iterator\RealIteratorTestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('Symfony\Component\Finder\Finder', Finder::create());
    }

    public function testDirectories()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->directories());
        $this->assertIterator($this->toAbsolute(array('foo', 'toto')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->directories();
        $finder->files();
        $finder->directories();
        $this->assertIterator($this->toAbsolute(array('foo', 'toto')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testFiles()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->files());
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'test.py', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->files();
        $finder->directories();
        $finder->files();
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'test.py', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testDepth()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->depth('< 1'));
        $this->assertIterator($this->toAbsolute(array('foo', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $this->assertSame($finder, $finder->depth('<= 0'));
        $this->assertIterator($this->toAbsolute(array('foo', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $this->assertSame($finder, $finder->depth('>= 1'));
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->depth('< 1')->depth('>= 1');
        $this->assertIterator(array(), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testName()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->name('*.php'));
        $this->assertIterator($this->toAbsolute(array('test.php')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->name('test.ph*');
        $finder->name('test.py');
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->name('~^test~i');
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->name('~\\.php$~i');
        $this->assertIterator($this->toAbsolute(array('test.php')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->name('test.p{hp,y}');
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testNotName()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->notName('*.php'));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->notName('*.php');
        $finder->notName('*.py');
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->name('test.ph*');
        $finder->name('test.py');
        $finder->notName('*.php');
        $finder->notName('*.py');
        $this->assertIterator(array(), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->name('test.ph*');
        $finder->name('test.py');
        $finder->notName('*.p{hp,y}');
        $this->assertIterator(array(), $finder->in(self::$tmpDir)->getIterator());
    }

    /**
     * @dataProvider getRegexNameTestData
     *
     * @group regexName
     */
    public function testRegexName($regex)
    {
        $finder = Finder::create();
        $finder->name($regex);
        $this->assertIterator($this->toAbsolute(array('test.py', 'test.php')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSize()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->files()->size('< 1K')->size('> 500'));
        $this->assertIterator($this->toAbsolute(array('test.php')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testDate()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->files()->date('until last month'));
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testExclude()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->exclude('foo'));
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testIgnoreVCS()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->ignoreVCS(false)->ignoreDotFiles(false));
        $this->assertIterator($this->toAbsolute(array('.git', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->ignoreVCS(false)->ignoreVCS(false)->ignoreDotFiles(false);
        $this->assertIterator($this->toAbsolute(array('.git', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $this->assertSame($finder, $finder->ignoreVCS(true)->ignoreDotFiles(false));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testIgnoreDotFiles()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->ignoreDotFiles(false)->ignoreVCS(false));
        $this->assertIterator($this->toAbsolute(array('.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $finder->ignoreDotFiles(false)->ignoreDotFiles(false)->ignoreVCS(false);
        $this->assertIterator($this->toAbsolute(array('.git', '.bar', '.foo', '.foo/.bar', '.foo/bar', 'foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());

        $finder = Finder::create();
        $this->assertSame($finder, $finder->ignoreDotFiles(true)->ignoreVCS(false));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSortByName()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->sortByName());
        $this->assertIterator($this->toAbsolute(array('foo', 'foo bar', 'foo/bar.tmp', 'test.php', 'test.py', 'toto')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSortByType()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->sortByType());
        $this->assertIterator($this->toAbsolute(array('foo', 'foo bar', 'toto', 'foo/bar.tmp', 'test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSortByAccessedTime()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->sortByAccessedTime());
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'toto', 'test.py', 'foo', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSortByChangedTime()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->sortByChangedTime());
        $this->assertIterator($this->toAbsolute(array('toto', 'test.py', 'test.php', 'foo/bar.tmp', 'foo', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSortByModifiedTime()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->sortByModifiedTime());
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'toto', 'test.py', 'foo', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testSort()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->sort(function (\SplFileInfo $a, \SplFileInfo $b) { return strcmp($a->getRealpath(), $b->getRealpath()); }));
        $this->assertIterator($this->toAbsolute(array('foo', 'foo bar', 'foo/bar.tmp', 'test.php', 'test.py', 'toto')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testFilter()
    {
        $finder = Finder::create();
        $this->assertSame($finder, $finder->filter(function (\SplFileInfo $f) { return preg_match('/test/', $f) > 0; }));
        $this->assertIterator($this->toAbsolute(array('test.php', 'test.py')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testFollowLinks()
    {
        if ('\\' == DIRECTORY_SEPARATOR) {
            return;
        }

        $finder = Finder::create();
        $this->assertSame($finder, $finder->followLinks());
        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'test.php', 'test.py', 'toto', 'foo bar')), $finder->in(self::$tmpDir)->getIterator());
    }

    public function testIn()
    {
        $finder = Finder::create();
        try {
            $finder->in('foobar');
            $this->fail('->in() throws a \InvalidArgumentException if the directory does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '->in() throws a \InvalidArgumentException if the directory does not exist');
        }

        $finder = Finder::create();
        $iterator = $finder->files()->name('*.php')->depth('< 1')->in(array(self::$tmpDir, __DIR__))->getIterator();

        $this->assertIterator(array(self::$tmpDir.DIRECTORY_SEPARATOR.'test.php', __DIR__.DIRECTORY_SEPARATOR.'FinderTest.php'), $iterator);
    }

    public function testInWithGlob()
    {
        $finder = Finder::create();
        $finder->in(array(__DIR__.'/Fixtures/*/B/C', __DIR__.'/Fixtures/*/*/B/C'))->getIterator();

        $this->assertIterator($this->toAbsoluteFixtures(array('A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy')), $finder);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInWithNonDirectoryGlob()
    {
        $finder = Finder::create();
        $finder->in(__DIR__.'/Fixtures/A/a*');
    }

    public function testInWithGlobBrace()
    {
        $finder = Finder::create();
        $finder->in(array(__DIR__.'/Fixtures/{A,copy/A}/B/C'))->getIterator();

        $this->assertIterator($this->toAbsoluteFixtures(array('A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy')), $finder);
    }

    public function testGetIterator()
    {
        $finder = Finder::create();
        try {
            $finder->getIterator();
            $this->fail('->getIterator() throws a \LogicException if the in() method has not been called');
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e, '->getIterator() throws a \LogicException if the in() method has not been called');
        }

        $finder = Finder::create();
        $dirs = array();
        foreach ($finder->directories()->in(self::$tmpDir) as $dir) {
            $dirs[] = (string) $dir;
        }

        $expected = $this->toAbsolute(array('foo', 'toto'));

        sort($dirs);
        sort($expected);

        $this->assertEquals($expected, $dirs, 'implements the \IteratorAggregate interface');

        $finder = Finder::create();
        $this->assertEquals(2, iterator_count($finder->directories()->in(self::$tmpDir)), 'implements the \IteratorAggregate interface');

        $finder = Finder::create();
        $a = iterator_to_array($finder->directories()->in(self::$tmpDir));
        $a = array_values(array_map(function ($a) { return (string) $a; }, $a));
        sort($a);
        $this->assertEquals($expected, $a, 'implements the \IteratorAggregate interface');
    }

    public function testRelativePath()
    {
        $finder = Finder::create()->in(self::$tmpDir);

        $paths = array();

        foreach ($finder as $file) {
            $paths[] = $file->getRelativePath();
        }

        $ref = array("", "", "", "", "foo", "");

        sort($ref);
        sort($paths);

        $this->assertEquals($ref, $paths);
    }

    public function testRelativePathname()
    {
        $finder = Finder::create()->in(self::$tmpDir)->sortByName();

        $paths = array();

        foreach ($finder as $file) {
            $paths[] = $file->getRelativePathname();
        }

        $ref = array("test.php", "toto", "test.py", "foo", "foo".DIRECTORY_SEPARATOR."bar.tmp", "foo bar");

        sort($paths);
        sort($ref);

        $this->assertEquals($ref, $paths);
    }

    public function testAppendWithAFinder()
    {
        $finder = Finder::create();
        $finder->files()->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder1 = Finder::create();
        $finder1->directories()->in(self::$tmpDir);

        $finder = $finder->append($finder1);

        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'toto')), $finder->getIterator());
    }

    public function testAppendWithAnArray()
    {
        $finder = Finder::create();
        $finder->files()->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder->append($this->toAbsolute(array('foo', 'toto')));

        $this->assertIterator($this->toAbsolute(array('foo', 'foo/bar.tmp', 'toto')), $finder->getIterator());
    }

    public function testAppendReturnsAFinder()
    {
        $this->assertInstanceOf('Symfony\\Component\\Finder\\Finder', Finder::create()->append(array()));
    }

    public function testAppendDoesNotRequireIn()
    {
        $finder = Finder::create();
        $finder->in(self::$tmpDir.DIRECTORY_SEPARATOR.'foo');

        $finder1 = Finder::create()->append($finder);

        $this->assertIterator(iterator_to_array($finder->getIterator()), $finder1->getIterator());
    }

    public function testCountDirectories()
    {
        $directory = Finder::create()->directories()->in(self::$tmpDir);
        $i = 0;

        foreach ($directory as $dir) {
            $i++;
        }

        $this->assertCount($i, $directory);
    }

    public function testCountFiles()
    {
        $files = Finder::create()->files()->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures');
        $i = 0;

        foreach ($files as $file) {
            $i++;
        }

        $this->assertCount($i, $files);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCountWithoutIn()
    {
        $finder = Finder::create()->files();
        count($finder);
    }

    /**
     * @dataProvider getContainsTestData
     * @group grep
     */
    public function testContains($matchPatterns, $noMatchPatterns, $expected)
    {
        $finder = Finder::create();
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures')
            ->name('*.txt')->sortByName()
            ->contains($matchPatterns)
            ->notContains($noMatchPatterns);

        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function testContainsOnDirectory()
    {
        $finder = Finder::create();
        $finder->in(__DIR__)
            ->directories()
            ->name('Fixtures')
            ->contains('abc');
        $this->assertIterator(array(), $finder);
    }

    public function testNotContainsOnDirectory()
    {
        $finder = Finder::create();
        $finder->in(__DIR__)
            ->directories()
            ->name('Fixtures')
            ->notContains('abc');
        $this->assertIterator(array(), $finder);
    }

    public function testFollowSymlink()
    {
        $done = symlink(self::toAbsolute('foo'), self::toAbsolute('foo_link'));

        if (!$done) {
            $this->markTestSkipped('Symbolic links are not available on this OS.');
        }

        $finder = Finder::create();
        $finder->files()->in(self::$tmpDir);
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'test.py', 'foo bar')), $finder->getIterator());

        $finder = Finder::create();
        $finder->files()->followLinks()->in(self::$tmpDir);
        $this->assertIterator($this->toAbsolute(array('foo/bar.tmp', 'test.php', 'test.py', 'foo bar', 'foo_link/bar.tmp')), $finder->getIterator());

        unlink(self::toAbsolute('foo_link'));
    }

    /**
     * Searching in multiple locations involves AppendIterator which does an unnecessary rewind which leaves FilterIterator
     * with inner FilesystemIterator in an invalid state.
     *
     * @see https://bugs.php.net/bug.php?id=49104
     */
    public function testMultipleLocations()
    {
        $locations = array(
            self::$tmpDir.'/',
            self::$tmpDir.'/toto/',
        );

        // it is expected that there are test.py test.php in the tmpDir
        $finder = Finder::create();
        $finder->in($locations)->depth('< 1')->name('test.php');

        $this->assertCount(1, $finder);
    }

    /**
     * Iterator keys must be the file pathname.
     */
    public function testIteratorKeys()
    {
        $finder = Finder::create()->in(self::$tmpDir);
        foreach ($finder as $key => $file) {
            $this->assertEquals($file->getPathname(), $key);
        }
    }

    public function getContainsTestData()
    {
        return array(
            array('', '', array()),
            array('foo', 'bar', array()),
            array('', 'foobar', array('dolor.txt', 'ipsum.txt', 'lorem.txt')),
            array('lorem ipsum dolor sit amet', 'foobar', array('lorem.txt')),
            array('sit', 'bar', array('dolor.txt', 'ipsum.txt', 'lorem.txt')),
            array('dolor sit amet', '@^L@m', array('dolor.txt', 'ipsum.txt')),
            array('/^lorem ipsum dolor sit amet$/m', 'foobar', array('lorem.txt')),
            array('lorem', 'foobar', array('lorem.txt')),
            array('', 'lorem', array('dolor.txt', 'ipsum.txt')),
            array('ipsum dolor sit amet', '/^IPSUM/m', array('lorem.txt')),
        );
    }

    public function getRegexNameTestData()
    {
        return array(
            array('~.+\\.p.+~i'),
            array('~t.*s~i'),
        );
    }

    /**
     * @dataProvider getTestPathData
     */
    public function testPath($matchPatterns, $noMatchPatterns, array $expected)
    {
        $finder = Finder::create();
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'Fixtures')
            ->path($matchPatterns)
            ->notPath($noMatchPatterns);

        $this->assertIterator($this->toAbsoluteFixtures($expected), $finder);
    }

    public function getTestPathData()
    {
        return array(
            array('', '', array()),
            array('/^A\/B\/C/', '/C$/',
                array('A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat')
            ),
            array('/^A\/B/', 'foobar',
                array(
                    'A'.DIRECTORY_SEPARATOR.'B',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                )
            ),
            array('A/B/C', 'foobar',
                array(
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat.copy',
                )
            ),
            array('A/B', 'foobar',
                array(
                    //dirs
                    'A'.DIRECTORY_SEPARATOR.'B',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C',
                    //files
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat',
                    'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'ab.dat.copy',
                    'copy'.DIRECTORY_SEPARATOR.'A'.DIRECTORY_SEPARATOR.'B'.DIRECTORY_SEPARATOR.'C'.DIRECTORY_SEPARATOR.'abc.dat.copy',
                )
            ),
            array('/^with space\//', 'foobar',
                array(
                    'with space'.DIRECTORY_SEPARATOR.'foo.txt',
                )
            ),
        );
    }

    public function testAccessDeniedException()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('chmod is not supported on Windows');
        }

        $finder = Finder::create();
        $finder->files()->in(self::$tmpDir);

        // make 'foo' directory non-readable
        chmod(self::$tmpDir.DIRECTORY_SEPARATOR.'foo', 0333);

        try {
            $this->assertIterator($this->toAbsolute(array('foo bar', 'test.php', 'test.py')), $finder->getIterator());
            $this->fail('Finder should throw an exception when opening a non-readable directory.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\\Component\\Finder\\Exception\\AccessDeniedException', $e);
        }

        // restore original permissions
        chmod(self::$tmpDir.DIRECTORY_SEPARATOR.'foo', 0777);
    }

    public function testIgnoredAccessDeniedException()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('chmod is not supported on Windows');
        }

        $finder = Finder::create();
        $finder->files()->ignoreUnreadableDirs()->in(self::$tmpDir);

        // make 'foo' directory non-readable
        chmod(self::$tmpDir.DIRECTORY_SEPARATOR.'foo', 0333);

        $this->assertIterator($this->toAbsolute(array('foo bar', 'test.php', 'test.py')), $finder->getIterator());

        // restore original permissions
        chmod(self::$tmpDir.DIRECTORY_SEPARATOR.'foo', 0777);
    }

    /**
     * Searching in multiple locations with sub directories involves
     * AppendIterator which does an unnecessary rewind which leaves
     * FilterIterator with inner FilesystemIterator in an invalid state.
     *
     * @see https://bugs.php.net/bug.php?id=49104
     */
    public function testMultipleLocationsWithSubDirectories()
    {
        $locations = array(
            __DIR__.'/Fixtures/one',
            self::$tmpDir.DIRECTORY_SEPARATOR.'toto',
        );

        $finder = new Finder();
        $finder->in($locations)->depth('< 10')->name('*.neon');

        $expected = array(
            __DIR__.'/Fixtures/one'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'c.neon',
            __DIR__.'/Fixtures/one'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'d.neon',
        );

        $this->assertIterator($expected, $finder);
        $this->assertIteratorInForeach($expected, $finder);
    }

    public function testNonSeekableStream()
    {
        if (!in_array('ftp', stream_get_wrappers())) {
            $this->markTestSkipped(sprintf('Unavailable stream "%s".', 'ftp'));
        }

        try {
            $i = Finder::create()->in('ftp://ftp.mozilla.org/')->depth(0)->getIterator();
        } catch (\UnexpectedValueException $e) {
            $this->markTestSkipped(sprintf('Unsupported stream "%s".', 'ftp'));
        }

        $contains = array(
            'ftp://ftp.mozilla.org'.DIRECTORY_SEPARATOR.'README',
            'ftp://ftp.mozilla.org'.DIRECTORY_SEPARATOR.'index.html',
            'ftp://ftp.mozilla.org'.DIRECTORY_SEPARATOR.'pub',
        );

        $this->assertIteratorInForeach($contains, $i);
    }
}
