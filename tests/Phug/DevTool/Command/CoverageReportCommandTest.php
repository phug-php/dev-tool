<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\CoverageReportCommand;

/**
 * Class CoverageReportCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CoverageReportCommand
 */
class CoverageReportCommandTest extends \PHPUnit_Framework_TestCase
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
