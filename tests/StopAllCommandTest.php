<?php

/**
 * This file is part of the LaravelSingleInstanceCommand library.
 *
 * (c) Denis Milovanov <me@denismilovanov.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LaravelSingleInstanceCommand;

use Symfony\Component\Process\Process;

class StopAllCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        putenv('APP_ENV=testing');

        $cmd = 'pids:test-single-instance';

        $pid1 = Command::getPidFile($cmd, 1);
        $this->assertTrue(! file_exists($pid1));

        $pid2 = Command::getPidFile($cmd, 2);
        $this->assertTrue(! file_exists($pid2));

        $p1 = new Process('php tests/artisan ' . $cmd . ' p=1 sleep=100');
        $p1->start();
        $p2 = new Process('php tests/artisan ' . $cmd . ' p=2 sleep=100');
        $p2->start();

        sleep(2);
        $this->assertTrue(file_exists($pid1)); // still sleeping
        $this->assertTrue(file_exists($pid2)); // still sleeping

        $cmd = 'pids:stop-all';

        $stop_process = new Process('php tests/artisan ' . $cmd);
        $stop_process->run();

        $this->assertTrue(! file_exists($pid1)); // stopped
        $this->assertTrue(! file_exists($pid2)); // stopped
    }
}
