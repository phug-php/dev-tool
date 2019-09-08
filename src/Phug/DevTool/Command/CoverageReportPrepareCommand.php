<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageReportPrepareCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('coverage:report:prepare')
            ->setHelp('This command install the coverage report utils.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpVersion = $input->getOption('php-version');

        if (!empty($phpVersion)) {
            if (!preg_match('/^'.preg_quote($phpVersion).'(\D.*)?$/', PHP_VERSION)) {
                $output->writeln(
                    'Test report ignored since PHP version ('.PHP_VERSION.')'.
                    ' does not match '.$phpVersion.'.'
                );

                return 0;
            }
            $output->writeln(
                '<fg=green>Proceed test report since PHP version ('.PHP_VERSION.') '.
                'matches '.$phpVersion.'.</>'
            );
        }

        return $this->getApplication()->runCoverageReporterPreparation();
    }
}
