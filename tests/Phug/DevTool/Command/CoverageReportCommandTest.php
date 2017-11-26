<?php

namespace Phug\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Phug\DevTool\Command\CoverageReportCommand;

/**
 * Class CoverageReportCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CoverageReportCommand
 */
class CoverageReportCommandTest extends TestCase
{
    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $coverageReport = new CoverageReportCommand();

        self::assertSame('coverage:report', $coverageReport->getName());
    }
}
