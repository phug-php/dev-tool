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
            ->setDescription('Runs all necessary checks.')
            ->setHelp('Runs all necessary checks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();
        $coverageFilePath = $app->getWorkingDirectory().'/coverage.xml';

        $unitTestsCode = $app->runCommand('unit-tests:run', $output, [
            '--coverage-text' => true,
            '--coverage-clover' => $coverageFilePath,
        ]);

        if (!$app->isHhvm()) {
            $coverageCode = $app->runCommand('coverage:check', $output, [
                'input-file' => $coverageFilePath,
            ]);

            if ($coverageCode === 0 && $input->getOption('report')) {
                $coverageCode = $app->runCommand('coverage:report', $output, [
                    'input-file' => $coverageFilePath,
                    '--php-version', '5.6',
                ]);
            }
        }

        if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
            $codeStyleCode = $app->runCommand('code-style:check', $output, [
                '--no-interaction',
            ]);
        }

        if (file_exists($coverageFilePath)) {
            unlink($coverageFilePath);
        }

        return $unitTestsCode ?: $coverageCode ?: $codeStyleCode;
    }
}
