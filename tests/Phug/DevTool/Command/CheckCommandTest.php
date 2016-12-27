<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Application;
use Phug\DevTool\Command\CheckCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

    /**
    * @covers ::execute
    */
    public function testExecute()
    {
        $input = new StringInput('check');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);

        self::assertSame(0, $app->run($input, $buffer));
        self::assertRegExp('/Code looks great\. Go on!/', $buffer->fetch());
    }
}
