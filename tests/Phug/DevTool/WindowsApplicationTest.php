<?php

namespace Phug\Test\DevTool;

use Phug\DevTool\Application;

class WindowsApplicationTest extends Application
{
    public function isWindows()
    {
        return true;
    }

    public function getPhpcsPath()
    {
        return $this->getShellCommandPath('vendor/bin/phpcs');
    }
}
