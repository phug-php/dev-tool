<?php

namespace Phug\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Phug\DevTool\Command\CoverageCheckCommand;

/**
 * Class CoverageCheckCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CoverageCheckCommand
 */
class CoverageCheckCommandTest extends TestCase
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
