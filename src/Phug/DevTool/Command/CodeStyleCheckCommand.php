<?php

namespace Phug\DevTool\Command;

use Phug\DevTool\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CodeStyleCheckCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('code-style:check')
            ->addOption('ignore-tests', null, InputOption::VALUE_NONE, 'Ignore /tests/ directories')
            ->addOption('ignore-debug', null, InputOption::VALUE_NONE, 'Ignore /debug/ directories')
            ->setDescription('Runs code style checker (phpcs).')
            ->setHelp('This command runs the code style checker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $args = [
            '--standard' => $this->getApplication()->getConfigFilePath('phpcs.xml'),
        ];
        $ignore = [];
        if ($input->getOption('ignore-tests')) {
            $ignore[] = '*/tests/*';
        }
        if ($input->getOption('ignore-debug')) {
            $ignore[] = '*/debug/*';
        }
        if (count($ignore)) {
            $args[] = '--ignore='.implode(',', $ignore);
        }

        if (($code = $this->getApplication()->runCodeStyleChecker($args)) === 0) {
            $output->writeln('<fg=green>Code looks great. Go on!</>');

            return $code;
        }

        if (!$helper->ask($input, $output, new ConfirmationQuestion(
            'There were code-style problems. Do you want to fix them automatically? [Y/N, Default: N] ',
            false
        ))
        ) {
            $output->writeln('You can always fix code-style problems by running the [code-style:fix] command.');

            return $code;
        }

        $code = $this->getApplication()->runCommand('code-style:fix', $output);
        $output->writeln('<fg=green>All fixable problems have been fixed.</>');

        if (!$helper->ask($input, $output, new ConfirmationQuestion(
            'Do you want to run the code-style check again? [Y/N, Default: N] ',
            false
        ))
        ) {
            return $code;
        }

        if (($code = $this->getApplication()->runCodeStyleChecker($args)) === 0) {
            $output->writeln('<fg=green>Code looks great. Go on!</>');

            return $code;
        }

        $output->writeln(
            '<fg=yellow>Code-style not optimal. '
            ."These problems can't be fixed by [code-style:fix]. Fix them yourself.</>"
        );

        return $code;
    }
}
