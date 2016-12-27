<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('install')
            ->setDescription('Updates and installs composer dependencies.')
            ->setHelp('Updates and installs composer dependencies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();

        if ($code = $app->runShellCommand('composer', ['self-update'])) {
            return $code;
        }

        return $app->runShellCommand('composer', ['install']);
    }
}
