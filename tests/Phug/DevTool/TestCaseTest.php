<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\TestCase;

/**
 * Class TestCaseTest.
 *
 * @coversDefaultClass \Phug\DevTool\TestCase
 */
class TestCaseTest extends TestCase
{
    /**
     * @covers ::<public>
     */
    public function testCleanupTempDirectory()
    {
        $foo = sys_get_temp_dir().DIRECTORY_SEPARATOR.'foo';
        $bar = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bar';
        file_put_contents($foo, 'FOO');
        $this->saveTempDirectoryFilesList();
        file_put_contents($bar, 'BAR');
        $this->cleanupTempDirectory();
        clearstatcache();

        self::assertFileExists($foo);
        self::assertFileNotExists($bar);

        unlink($foo);
    }

    /**
     * @covers ::removeFile
     * @covers ::emptyDirectory
     */
    public function testEmptyDirectory()
    {
        $foo = sys_get_temp_dir().DIRECTORY_SEPARATOR.'foo';
        $bar = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bar';
        file_put_contents($foo, 'FOO');

        self::assertNull($this->emptyDirectory($foo));

        mkdir($bar);
        mkdir("$bar/biz");
        file_put_contents("$bar/xx", 'xx');
        file_put_contents("$bar/biz/yy", 'yy');

        self::assertNull($this->emptyDirectory($bar));
        self::assertSame(['.', '..'], scandir($bar));
    }
}
