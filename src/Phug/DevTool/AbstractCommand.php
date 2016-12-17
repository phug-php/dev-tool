<?php

namespace Phug\DevTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 *
 * @package Phug\DevTool
 * @method Application getApplication()
 */
abstract class AbstractCommand extends Command
{

    protected function configure()
    {
        parent::configure();
    }

    public function run(InputInterface $input, OutputInterface $output)
    {

        return parent::run($input, $output);
    }
}