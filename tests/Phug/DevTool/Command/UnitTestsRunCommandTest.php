<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\UnitTestsRunCommand;

/**
 * Class UnitTestsRunCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\UnitTestsRunCommand
 */
class UnitTestsRunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers ::configure
    */
    public function testConfigure()
    {
        $unitTests = new UnitTestsRunCommand();

        self::assertSame('unit-tests:run', $unitTests->getName());
    }
}
