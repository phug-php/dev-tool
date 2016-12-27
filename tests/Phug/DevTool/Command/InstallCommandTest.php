<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Command\InstallCommand;

/**
 * Class InstallCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\InstallCommand
 */
class InstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers ::configure
    */
    public function testConfigure()
    {
        $install = new InstallCommand();

        self::assertSame('install', $install->getName());
    }
}
