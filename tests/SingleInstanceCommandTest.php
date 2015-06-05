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

class LaravelSingleInstanceCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        putenv('APP_ENV=testing');

        $cmd = 'pids:test-single-instance';

        $pid1 = Command::getPidFile($cmd, 1);
        $this->assertTrue(! file_exists($pid1));

        $pid2 = Command::getPidFile($cmd, 2);
        $this->assertTrue(! file_exists($pid2));

        $p1 = new Process('php tests/artisan ' . $cmd . ' p=1 sleep=3');
        $p1->start();
        sleep(1);

        $p2 = new Process('php tests/artisan ' . $cmd . ' p=1 sleep=100'); // same process number
        $p2->start();

        sleep(1);
        $this->assertTrue(file_exists($pid1)); // still sleeping
        $this->assertTrue(! file_exists($pid2)); // has not been started

        sleep(3); // enough to complete 1st
    }
}
