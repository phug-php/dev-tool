<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\CodeStyleFixCommand;

/**
 * Class CodeStyleFixCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CodeStyleFixCommand
 */
class CodeStyleFixCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $codeStyleFix = new CodeStyleFixCommand();

        self::assertSame('code-style:fix', $codeStyleFix->getName());
    }
}
