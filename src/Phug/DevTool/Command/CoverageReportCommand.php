<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageReportCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('coverage:report')
            ->addArgument('input-file', InputArgument::REQUIRED, 'The XML file to report coverage from')
            ->setDescription('Reports coverage.')
            ->setHelp('This command reports coverage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $xmlFile = realpath($input->getOption('input-file'));

        $this->getApplication()->runVendorCommand('test-reporter', [
            "--coverage-report $xmlFile"
        ]);

        return 0;
    }
}
