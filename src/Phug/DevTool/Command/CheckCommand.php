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
            ->addOption('report', null, InputOption::VALUE_NONE)
            ->addOption('coverage-text', null, InputOption::VALUE_NONE, 'Display coverage info?')
            ->addOption('coverage-html', null, InputOption::VALUE_OPTIONAL, 'Save coverage info as HTML?', false)
            ->addOption('coverage-clover', null, InputOption::VALUE_OPTIONAL, 'Save coverage info as XML?', false)
            ->addOption('group', null, InputOption::VALUE_OPTIONAL, 'Excute only a tests group?', false)
            ->addOption('ignore-tests', null, InputOption::VALUE_NONE)
            ->setDescription('Runs all necessary checks.')
            ->setHelp('Runs all necessary checks');
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

        if (!$app->isHhvm()) {
            if (($code = $app->runCommand('coverage:check', $output, [
                'input-file' => $coverageFilePath,
            ])) !== 0) {
                return $code;
            }

            if ($input->getOption('report')) {
                if (($code = $app->runCommand('coverage:report', $output, [
                    'input-file' => $coverageFilePath,
                    '--php-version', '5.6',
                ])) !== 0) {
                    return $code;
                }
            }
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
