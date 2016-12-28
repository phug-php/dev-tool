<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\CodeStyleCheckCommand;

/**
 * Class CodeStyleCheckCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CodeStyleCheckCommand
 */
class CodeStyleCheckCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $codeStyleCheck = new CodeStyleCheckCommand();

        self::assertSame('code-style:check', $codeStyleCheck->getName());
    }
}
