<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\CoverageCheckCommand;

/**
 * Class CoverageCheckCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CoverageCheckCommand
 */
class CoverageCheckCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $coverageCheck = new CoverageCheckCommand();

        self::assertSame('coverage:check', $coverageCheck->getName());
    }
}
