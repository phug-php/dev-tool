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

        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            $app->runShellCommand('composer', ['require', 'phpunit/phpunit:^4.8']);
        }

        $app->runShellCommand('composer', ['require', 'codeclimate/php-test-reporter:@dev', '--dev']);

        return $app->runShellCommand('composer', ['install']);
    }
}
