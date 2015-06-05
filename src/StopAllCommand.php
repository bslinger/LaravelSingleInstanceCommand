<?php

namespace LaravelSingleInstanceCommand;

class StopAllCommand extends \Illuminate\Console\Command
{
    protected $name = 'pids:stop-all';
    protected $description = 'Stop all scripts by pid-files found in pid-directory';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $dir = Command::getPidDir();
        $pids = glob($dir . '*.pid');

        sort($pids);

        if (! $pids) {
            app('log')->info('There are no pids in ' . $dir . '.');
            return;
        }

        foreach ($pids as $pid_file) {
            $remove_pid = false;

            $pid = file_get_contents($pid_file);
            if (is_numeric($pid)) {
                $result = $pid != getmypid() ? posix_kill($pid, 15) : false;
                app('log')->info('Going to stop process, pid file is ' . $pid_file . ', pid is ' . $pid . '...');
                if ($result) {
                    app('log')->info('Successfully stopped, pid was ' . $pid . '.');
                } else {
                    app('log')->info('There is no process with pid ' . $pid . '.');
                    $remove_pid = true;
                }
            } else {
                app('log')->info('File ' . $pid_file . ' does not contain pid.');
                $remove_pid = true;
            }

            if ($remove_pid and file_exists($pid_file)) {
                unlink($pid_file);
                app('log')->info('Pid file ' . $pid_file . ' removed.');
            }
        }
    }
}
