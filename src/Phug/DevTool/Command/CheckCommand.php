<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('check')
            ->addOption(
                'report',
                null,
                InputOption::VALUE_NONE,
                'Send coverage report?'
            )
            ->addOption(
                'coverage-text',
                null,
                InputOption::VALUE_NONE,
                'Display coverage info?'
            )
            ->addOption(
                'coverage-html',
                null,
                InputOption::VALUE_OPTIONAL,
                'Save coverage info as HTML?',
                false
            )
            ->addOption(
                'coverage-clover',
                null,
                InputOption::VALUE_OPTIONAL,
                'Save coverage info as XML?',
                false
            )
            ->addOption(
                'group',
                null,
                InputOption::VALUE_OPTIONAL,
                'Excute only a tests group?',
                false
            )
            ->addOption(
                'ignore-tests',
                null,
                InputOption::VALUE_NONE,
                'Ignore /tests/ directories'
            )
            ->addOption(
                'ignore-debug',
                null,
                InputOption::VALUE_NONE,
                'Ignore /debug/ directories'
            )
            ->addOption(
                'coverage-php-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'If specified, the coverage is only tested for the given PHP version'
            )
            ->setDescription('Runs all necessary checks.')
            ->setHelp('Runs all necessary checks');
    }

    protected function runCoverage(InputInterface $input, OutputInterface $output, $coverageFilePath)
    {
        $app = $this->getApplication();

        $phpVersion = $input->getOption('coverage-php-version');

        if (!empty($phpVersion)) {
            if (!preg_match('/^'.preg_quote($phpVersion).'(\D.*)?$/', PHP_VERSION)) {
                $output->writeln(
                    'Coverage ignored since PHP version ('.PHP_VERSION.')'.
                    ' does not match '.$phpVersion.'.'
                );

                return 0;
            }
            $output->writeln(
                '<fg=green>Proceed test report since PHP version ('.PHP_VERSION.') '.
                'matches '.$phpVersion.'.</>'
            );
        }

        if (($code = $app->runCommand('coverage:check', $output, [
            'input-file' => $coverageFilePath,
        ])) !== 0) {
            return $code;
        }

        if (($code = $app->runCommand('coverage:report', $output, [
            'input-file' => $coverageFilePath,
        ])) !== 0) {
            return $code;
        }

        return 0;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();
        $coverageFilePath = $app->getWorkingDirectory().'/coverage.xml';

        $args = [
            '--coverage-text'   => true,
            '--coverage-clover' => $coverageFilePath,
        ];

        $passTrough = [
            'coverage-text',
            'coverage-clover',
            'coverage-html',
            'group',
        ];

        foreach ($passTrough as $option) {
            if ($value = $input->getOption($option)) {
                $args[$option] = $value;
            }
        }

        if (($code = $app->runCommand('unit-tests:run', $output, $args)) !== 0) {
            return $code;
        }

        if (!$app->isHhvm() && ($code = $this->runCoverage($input, $output, $coverageFilePath))) {
            return $code;
        }

        if (version_compare(PHP_VERSION, '5.6.0') >= 0 && ($code = $app->runCommand('code-style:check', $output, [
            '--no-interaction',
            '--ignore-tests' => $input->getOption('ignore-tests'),
        ])) !== 0) {
            return $code;
        }

        if (file_exists($coverageFilePath)) {
            unlink($coverageFilePath);
        }

        return 0;
    }
}
