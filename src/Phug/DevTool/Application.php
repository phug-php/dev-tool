<?php

namespace Phug\DevTool;

use Phug\DevTool\Command\CheckCommand;
use Phug\DevTool\Command\CodeStyleCheckCommand;
use Phug\DevTool\Command\CodeStyleFixCommand;
use Phug\DevTool\Command\CoverageCheckCommand;
use Phug\DevTool\Command\CoverageReportCommand;
use Phug\DevTool\Command\CoverageReportPrepareCommand;
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
        $this->add(new CoverageReportPrepareCommand());
        $this->add(new InstallCommand());
        $this->add(new UnitTestsRunCommand());
    }

    public function getWorkingDirectory()
    {
        return getcwd();
    }

    protected function getConfigDirectory()
    {
        return realpath(__DIR__.'/../../../config');
    }

    public function getConfigFilePath($fileName)
    {
        $localPath = realpath($this->getWorkingDirectory().DIRECTORY_SEPARATOR.$fileName);

        if ($localPath) {
            return $localPath;
        }

        return $this->getConfigDirectory().DIRECTORY_SEPARATOR.$fileName;
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

    protected function getShellCommandPath($command)
    {
        $cwd = $this->getWorkingDirectory();
        $commandPath = $cwd."/$command";

        //Check if there is a windows batch file equivalent for composer commands
        if ($this->isWindows() && ($batPath = realpath("$commandPath.bat"))) {
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

    public function runCoverageReporterPreparation()
    {
        $url = 'https://codeclimate.com/downloads/test-reporter/test-reporter-latest-linux-amd64';

        return $this->runShellCommand(implode(' && ', [
            "curl -L $url > ./cc-test-reporter",
            'chmod +x ./cc-test-reporter',
            './cc-test-reporter before-build',
        ]));
    }

    public function runCoverageReporter()
    {
        $clover = file_exists('clover.xml');
        $coverage = file_exists('coverage.xml');

        if (!$clover && !$coverage) {
            return 0; // No report to send
        } elseif (!$clover && $coverage) {
            copy('coverage.xml', 'clover.xml');
        } elseif (!$coverage && $clover) {
            copy('clover.xml', 'coverage.xml');
        }

        return $this->runShellCommand(implode(' && ', [
            'bash <(curl -s https://codecov.io/bash)',
            './cc-test-reporter after-build --coverage-input-type clover --exit-code $TRAVIS_TEST_RESULT',
            'composer require codacy/coverage',
            'vendor/bin/codacycoverage clover coverage.xml',
        ]));
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->configure();

        return parent::run($input, $output);
    }
}
