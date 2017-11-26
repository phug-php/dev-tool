<?php

namespace Phug\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Phug\DevTool\Command\InstallCommand;

/**
 * Class InstallCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\InstallCommand
 */
class InstallCommandTest extends TestCase
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
