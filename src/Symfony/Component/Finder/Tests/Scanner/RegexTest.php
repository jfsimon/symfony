<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Scanner;

use Symfony\Component\Finder\Scanner\Regex;

class RegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getStartingEndingData
     */
    public function testStartingEnding($pattern, $start, $end)
    {
        $regex = new Regex($pattern);

        $this->assertEquals($start, $regex->isStarting());
        $this->assertEquals($end, $regex->isEnding());
    }

    public function getStartingEndingData()
    {
        return array(
            array('~^abc~', true, false),
            array('~abc$~', false, true),
            array('~abc~', false, false),
            array('~^abc$~', true, true),
            array('~^abc\\$~', true, false),
        );
    }
}
