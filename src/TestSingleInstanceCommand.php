<?php

namespace LaravelSingleInstanceCommand;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

class TestSingleInstanceCommand extends LaravelSingleInstanceCommand
{
    protected $name = 'pids:test-single-instance';
    protected $description = 'Test single instance command';

    public function __construct()
    {
        parent::__construct();
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->checkInstance($input);
        $sleep = $input->getParameterOption('sleep', 0);
        if ($sleep) {
            sleep($sleep);
        }
    }
}
