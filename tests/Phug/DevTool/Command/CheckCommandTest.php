<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\CheckCommand;

/**
 * Class CheckCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CheckCommand
 */
class CheckCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers ::configure
    */
    public function testConfigure()
    {
        $check = new CheckCommand();

        self::assertSame('check', $check->getName());
    }
}
