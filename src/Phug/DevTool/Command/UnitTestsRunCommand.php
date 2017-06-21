<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UnitTestsRunCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('unit-tests:run')
            ->addOption('coverage-text', null, InputOption::VALUE_NONE, 'Display coverage info?')
            ->addOption('coverage-html', null, InputOption::VALUE_OPTIONAL, 'Save coverage info as HTML?', false)
            ->addOption('coverage-clover', null, InputOption::VALUE_OPTIONAL, 'Save coverage info as XML?', false)
            ->addOption('group', null, InputOption::VALUE_OPTIONAL, 'Excute only a tests group?', false)
            ->setDescription('Runs unit tests (phpunit).')
            ->setHelp('This command runs the unit tests');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $args = [
            '--verbose',
            '--configuration' => $this->getApplication()->getConfigFilePath('phpunit.xml'),
        ];

        if ($input->getOption('coverage-text')) {
            $args[] = '--coverage-text';
        }

        if ($path = $input->getOption('coverage-clover')) {
            $args['--coverage-clover'] = $path;
        }

        if ($path = $input->getOption('coverage-html')) {
            $args['--coverage-html'] = $path;
        }

        if ($group = $input->getOption('group')) {
            $args['--group'] = $group;
        }

        return $this->getApplication()->runUnitTests($args);
    }
}
