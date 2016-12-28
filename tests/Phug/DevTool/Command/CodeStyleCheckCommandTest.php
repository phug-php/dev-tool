<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Application;
use Phug\DevTool\Command\CodeStyleCheckCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $cwd = getcwd();
        chdir(__DIR__.'/../../../app');
        $input = new StringInput('code-style:check');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        $code = $app->run($input, $buffer);
        chdir($cwd);

        self::assertSame(0, $code);
        self::assertRegExp('/Code looks great\. Go on!/', $buffer->fetch());
    }
}
