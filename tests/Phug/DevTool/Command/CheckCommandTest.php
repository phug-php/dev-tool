<?php

namespace Phug\Test\DevTool;

use PHPUnit\Framework\TestCase;
use Phug\DevTool\Application;
use Phug\DevTool\Command\CheckCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class CheckCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\CheckCommand
 */
class CheckCommandTest extends TestCase
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
     * @covers ::runCoverage
     * @covers ::execute
     */
    public function testExecute()
    {
        $cwd = getcwd();
        chdir(__DIR__.'/../../../app');
        foreach (glob(__DIR__.'/../../../app/vendor/bin/*') as $file) {
            chmod($file, 0777);
        }
        $input = new StringInput('check');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        ob_start();
        $app->run($input, $buffer);
        ob_end_clean();
        if (file_exists('coverage.xml')) {
            unlink('coverage.xml');
        }
        chdir($cwd);

        self::assertTrue(strpos($buffer->fetch(), 'Error: Code coverage files not found. Please run `unit-tests:run`') !== false);
    }
}
