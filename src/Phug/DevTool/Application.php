<?php

namespace Phug\DevTool;

use Phug\DevTool\Command\CheckCommand;
use Phug\DevTool\Command\CodeStyleCheckCommand;
use Phug\DevTool\Command\CodeStyleFixCommand;
use Phug\DevTool\Command\CoverageCheckCommand;
use Phug\DevTool\Command\CoverageReportCommand;
use Phug\DevTool\Command\InstallCommand;
use Phug\DevTool\Command\UnitTestsRunCommand;
use RuntimeException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->configure();
    }

    protected function configure()
    {
        $this->add(new CheckCommand());
        $this->add(new CodeStyleCheckCommand());
        $this->add(new CodeStyleFixCommand());
        $this->add(new CoverageCheckCommand());
        $this->add(new CoverageReportCommand());
        $this->add(new InstallCommand());
        $this->add(new UnitTestsRunCommand());
    }

    public function getWorkingDirectory()
    {
        return getcwd();
    }

    public function getConfigDirectory()
    {
        return realpath(__DIR__.'/../../../config');
    }

    public function getConfigFilePath($fileName)
    {
        $localPath = realpath($this->getWorkingDirectory()."/$fileName");

        if ($localPath) {
            return $localPath;
        }

        return $this->getConfigDirectory()."/$fileName";
    }

    public function isWindows()
    {
        return strncmp(strtolower(PHP_OS), 'win', 3) === 0;
    }

    public function isUnix()
    {
        return !$this->isWindows();
    }

    public function isHhvm()
    {
        return defined('HHVM_VERSION');
    }

    public function runCommand($command, OutputInterface $output, array $arguments = null)
    {
        $arguments = $arguments ?: [];

        $command = $this->find($command);
        $arguments['command'] = $command->getName();

        return $command->run(new ArrayInput($arguments), $output);
    }

    public function getShellCommandPath($command)
    {
        $cwd = $this->getWorkingDirectory();
        $commandPath = $cwd."/$command";

        //Check if there is a windows batch file equivalent for composer commands
        if (($batPath = realpath("$commandPath.bat"))) {
            $commandPath = $batPath;
        }

        if (!($commandPath = realpath($commandPath))) {
            throw new RuntimeException(
                "The given command [$command] was not found"
            );
        }

        return $commandPath;
    }

    public function runShellCommand($command, array $arguments = null)
    {
        $arguments = $arguments ?: [];
        $parts = [escapeshellcmd($command)];

        foreach ($arguments as $key => $arg) {
            if (!is_int($key)) {
                $arg = "$key=$arg";
            }

            $parts[] = escapeshellarg($arg);
        }

        passthru(implode(' ', $parts), $returnCode);

        return is_numeric($returnCode) ? intval($returnCode) : $returnCode;
    }

    public function runVendorCommand($name, array $arguments = null)
    {
        return $this->runShellCommand($this->getShellCommandPath("vendor/bin/$name"), $arguments);
    }

    public function runUnitTests(array $arguments = null)
    {
        return $this->runVendorCommand('phpunit', $arguments);
    }

    public function runCodeStyleChecker(array $arguments = null)
    {
        $arguments = $arguments ?: [];

        $arguments[] = '--colors';

        return $this->runVendorCommand('phpcs', $arguments);
    }

    public function runCodeStyleFixer(array $arguments = null)
    {
        return $this->runVendorCommand('phpcbf', $arguments);
    }

    public function runCoverageReporter(array $arguments = null)
    {
        return $this->runVendorCommand('test-reporter', $arguments);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->configure();

        return parent::run($input, $output);
    }
}
