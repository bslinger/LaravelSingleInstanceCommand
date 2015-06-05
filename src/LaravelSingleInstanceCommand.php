<?php

namespace LaravelSingleInstanceCommand;

use Illuminate\Console\Command;

declare(ticks=1);

class LaravelSingleInstanceCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function getPidFile($command, $p = 1)
    {
        return self::getPidDir() . $command . '-' . $p . '.pid';
    }

    public static function getPidDir()
    {
        return sys_get_temp_dir() . '/' . env('APP_ENV') . '/';
    }

    private static function shutdown($reason, $pid)
    {
        app('log')->info($reason);
        if (file_exists($pid)) {
            unlink($pid);
            app('log')->info('Pid file was ' . $pid . '.');
        }
        app('log')->info('Process is going to successfully stop right now.');
        exit(0);
    }

    public function checkInstance($input)
    {
        $p = intval($input->getParameterOption('p', 1));
        $command = $input->getFirstArgument();

        $pid = self::getPidFile($command, $p);

        if (! file_exists($dir = dirname($pid))) {
            mkdir($dir, 0775, true);
        }

        if (file_exists($pid)) {
            app('log')->info('Process is already running, pid file is ' . $pid . ', pid is ' . file_get_contents($pid) . '.');
            exit(1);
        }

        file_put_contents($pid, posix_getpid());

        $shutdownSignal15 = function() use ($pid) {
            self::shutdown('SIGTERM received.', $pid);
        };

        $shutdownNormal = function() use ($pid) {
            self::shutdown('Normal termination.', $pid);
        };

        register_shutdown_function($shutdownNormal);
        pcntl_signal(15, $shutdownSignal15);
    }
}
