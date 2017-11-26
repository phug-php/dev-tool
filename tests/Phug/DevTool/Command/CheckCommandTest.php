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
        file_put_contents('coverage.xml', '<?xml version="1.0" encoding="UTF-8"?>
        <coverage generated="1482856255">
            <project timestamp="1482856255">
                <package name="Phug\DevTool">
                    <file name="src/Phug/DevTool/Application.php">
                        <class name="Application" namespace="Phug\DevTool">
                            <metrics complexity="26" methods="17" coveredmethods="17" conditionals="0" coveredconditionals="0" statements="49" coveredstatements="49" elements="66" coveredelements="66"/>
                        </class>
                        <line num="20" type="method" name="__construct" visibility="public" complexity="1" crap="1" count="1"/>
                        <line num="22" type="stmt" count="1"/>
                        <line num="24" type="stmt" count="1"/>
                        <line num="25" type="stmt" count="1"/>
                    </file>
                </package>
            </project>
        </coverage>');
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
