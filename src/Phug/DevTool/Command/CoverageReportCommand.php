<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageReportCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('coverage:report')
            ->addArgument('input-file', InputArgument::REQUIRED, 'The XML file to report coverage from')
            ->addOption('php-version', null, InputOption::VALUE_OPTIONAL)
            ->addArgument('php-version', InputArgument::OPTIONAL, 'If specified, the report is only send for the given PHP version')
            ->setDescription('Reports coverage.')
            ->setHelp('This command reports coverage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlFile = realpath($input->getArgument('input-file'));

        $phpVersion = $input->getOption('php-version');

        if (empty($phpVersion) || preg_match('/^'.preg_quote($phpVersion).'(\D.*)?$/', PHP_VERSION)) {
            $this->getApplication()->runVendorCommand('test-reporter', [
                "--coverage-report $xmlFile",
            ]);
        }

        return 0;
    }
}
