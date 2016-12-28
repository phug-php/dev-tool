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
        $cwd = getcwd();
        chdir(__DIR__.'/../../../app');
        foreach (glob(__DIR__.'/../../../app/vendor/bin/*') as $file) {
            chmod($file, 0777);
        }
        $input = new StringInput('check');
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        $code = $app->run($input, $buffer);
        if (file_exists('coverage.xml')) {
            unlink('coverage.xml');
        }
        chdir($cwd);

        self::assertSame(0, $code);
        $checkStyle = version_compare(PHP_VERSION, '5.6.0') >= 0;
        $expectedPattern = $checkStyle ? '/Code looks great\. Go on!/' : '/Code Coverage/';
        self::assertRegExp($expectedPattern, $buffer->fetch());
    }
}
