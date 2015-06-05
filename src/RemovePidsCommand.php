<?php

namespace LaravelSingleInstanceCommand;

use Illuminate\Console\Command;

class RemovePidsCommand extends Command
{
    protected $name = 'pids:remove';
    protected $description = 'Remove stolen pid-files from pid-directory';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $dir = LaravelSingleInstanceCommand::getPidDir();
        $pids = glob($dir . '*.pid');

        sort($pids);

        foreach ($pids as $pid_file) {
            if (! file_exists($pid_file)) {
                continue;
            }

            $pid = file_get_contents($pid_file);

            app('log')->info('Pid file ' . $pid_file . ' contains pid ' . $pid . '.');

            if ($pid) {
                $result = exec('ps --pid ' . $pid);
                $does_not_work = strpos($result, $pid) === false;
            } else {
                $does_not_work = true;
            }

            if ($does_not_work) {
                app('log')->info('Process with pid ' . $pid . ' is not working now.');

                if (file_exists($pid_file)) {
                    unlink($pid_file);
                    app('log')->info('Pid file ' . $pid_file . ' removed.');
                }
            }
        }
    }
}
