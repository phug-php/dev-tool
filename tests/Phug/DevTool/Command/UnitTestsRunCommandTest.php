<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Application;
use Phug\DevTool\Command\UnitTestsRunCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class UnitTestsRunCommandTest.
 *
 * @coversDefaultClass \Phug\DevTool\Command\UnitTestsRunCommand
 */
class UnitTestsRunCommandTest extends \PHPUnit_Framework_TestCase
{
    private static function remove($entity)
    {
        if (is_file($entity)) {
            return unlink($entity);
        }

        foreach (scandir($entity) as $file) {
            if ($file !== '.' && $file !== '..') {
                self::remove($entity.'/'.$file);
            }
        }
    }

    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $unitTests = new UnitTestsRunCommand();

        self::assertSame('unit-tests:run', $unitTests->getName());
    }

    /**
     * @group i
     * @covers ::execute
     */
    public function testExecute()
    {
        $cwd = getcwd();
        $app = realpath(__DIR__.'/../../../app');
        chdir($app);
        foreach (glob(__DIR__.'/../../../app/vendor/bin/*') as $file) {
            chmod($file, 0777);
        }
        $coverageHtml = $app.DIRECTORY_SEPARATOR.'coverage';
        $coverageClover = $app.DIRECTORY_SEPARATOR.'coverage.xml';
        if (file_exists($coverageClover)) {
            unlink($coverageClover);
        }
        $input = new StringInput(
            'unit-tests:run'.
            ' --coverage-text'.
            ' --coverage-html='.escapeshellarg(addslashes($coverageHtml)).
            ' --coverage-clover='.escapeshellarg(addslashes($coverageClover))
        );
        $buffer = new BufferedOutput();
        $app = new Application();
        $app->setAutoExit(false);
        $code = $app->run($input, $buffer);
        exit($buffer->fetch());
        self::assertTrue(file_exists($coverageHtml));
        self::assertTrue(file_exists($coverageClover));
        if (file_exists($coverageHtml)) {
            self::remove($coverageHtml);
        }
        if (file_exists($coverageClover)) {
            unlink($coverageClover);
        }
        chdir($cwd);

        self::assertRegExp('/test/', $buffer->fetch());
    }
}
