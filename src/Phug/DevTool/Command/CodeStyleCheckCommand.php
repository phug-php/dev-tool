<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CodeStyleCheckCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('code-style:check')
            ->setDescription('Runs code style checker (phpcs).')
            ->setHelp('This command runs the code style checker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $args = [
            '--standard' => $this->getApplication()->getConfigFilePath('phpcs.xml')
        ];

        if (($code = $this->getApplication()->runCodeStyleChecker($args)) === 0
            || !$input->getOption('interactive'))
            return $code;

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'There were code style problems. Do you want to fix them automatically?',
            false
        );

        if (!$helper->ask($input, $output, $question))
            return $code;

        return $this->getApplication()->runCommand('code-style:fix', $output);
    }
}