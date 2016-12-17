<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageCheckCommand extends AbstractCommand
{

    const DEFAULT_REQUIRED_COVERAGE = 80;

    protected function configure()
    {
        $this->setName('coverage:check')
            ->addArgument('input-file', InputArgument::REQUIRED, 'The XML file to check coverage on')
            ->addOption('required-coverage', null, InputOption::VALUE_OPTIONAL, 'The minimum coverage to pass', 80)
            ->setDescription('Checks coverage.')
            ->setHelp('This command checks coverage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $xmlFile = realpath($input->getOption('input-file'));
        $requiredCoverage = intval($input->getOption('required-coverage'));

        if (!$xmlFile) {

            $output->writeln('<fg=red>Error: Code coverage files not found. Please run `unit-tests:run`.</>');
            return 1;
        }

        $output->writeln('Validating code coverage...');

        $xml = new SimpleXMLElement(file_get_contents($xmlFile));
        $metrics = $xml->xpath('//metrics');
        $totalElements = 0;
        $checkedElements = 0;

        foreach ($metrics as $metric) {

            $totalElements   += (int)$metric['elements'];
            $checkedElements += (int)$metric['coveredelements'];
        }

        $coverage = ($checkedElements / $totalElements) * 100;

        if ($coverage < $requiredCoverage) {

            $output->writeln(
                "<fg=red>Fail: Code coverage is {$coverage}%. "
                ."You need to reach {$requiredCoverage}% to validate this build.</>"
            );

            return 1;
        }

        $output->writeln("<fg=green>Pass: Code Coverage {$coverage}%!</>");

        return 0;
    }
}