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

        if (($code = $app->runCommand('unit-tests:run', $output, [
            '--coverage-text' => true,
            '--coverage-clover' => $coverageFilePath
        ])) !== 0)
            return $code;

        if (($code = $app->runCommand('coverage:check', $output, [
            'input-file' => $coverageFilePath
        ])) !== 0)
            return $code;

        if ($input->getOption('report')) {
            if (($code = $app->runCommand('coverage:report', $output, [
                'input-file' => $coverageFilePath
            ])) !== 0)
                return $code;
        }

        if (($code = $app->runCommand('code-style:check', $output)) !== 0)
            return $code;

        if (file_exists($coverageFilePath))
            unlink($coverageFilePath);

        return 0;
    }
}