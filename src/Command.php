<?php

namespace LaravelSingleInstanceCommand;

declare(ticks=1);

class Command extends \Illuminate\Console\Command
{
    private static $verbose = false;

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

    private static function shutdown($reason, $pid_file)
    {
        app('log')->info($reason);
        if (file_exists($pid_file)) {
            unlink($pid_file);
            app('log')->info('Pid file was ' . $pid_file . '.');
        }
        app('log')->info('Process is going to successfully stop right now.');
        exit(0);
    }

    public function checkInstance($input)
    {
        $p = intval($input->getParameterOption('p', 1));
        $command = $input->getFirstArgument();

        $pid_file = self::getPidFile($command, $p);

        if (! file_exists($dir = dirname($pid_file))) {
            mkdir($dir, 0775, true);
        }

        self::$verbose = $input->hasParameterOption('v');

        if ($input->hasParameterOption('stop')) {
            if (file_exists($pid_file)) {
                $pid = file_get_contents($pid_file);
                $result = posix_kill($pid, 15);
                if ($result) {
                    app('log')->info('Term signal has been sent, pid file is ' . $pid_file . ', pid is ' . $pid . '.');
                } else {
                    app('log')->info('Unable to send term signal.');
                }
            } else {
                app('log')->info('There is no pid file ' . $pid_file);
            }
            exit(0);
        }

        if (file_exists($pid_file)) {
            if (self::$verbose) {
                $pid = file_get_contents($pid_file);
                app('log')->info('Process is already running, pid file is ' . $pid_file . ', pid is ' . $pid . '.');
            }
            exit(1);
        }

        $pid = posix_getpid();
        file_put_contents($pid_file, $pid);

        $shutdownSignal15 = function() use ($pid_file) {
            self::shutdown('SIGTERM received.', $pid_file);
        };

        $shutdownNormal = function() use ($pid_file) {
            self::shutdown('Normal termination.', $pid_file);
        };

        register_shutdown_function($shutdownNormal);
        pcntl_signal(15, $shutdownSignal15);

        app('log')->info('Process has been started, pid file is ' . $pid_file . ', pid is ' . $pid . '.');
    }
}
