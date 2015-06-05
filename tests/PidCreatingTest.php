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

class PidCreatingTest extends \PHPUnit_Framework_TestCase
{
    public function test1()
    {
        putenv('APP_ENV=testing');

        $cmd = 'pids:test-single-instance';
        $pid = LaravelSingleInstanceCommand::getPidFile($cmd);

        $this->assertTrue(! file_exists($pid));
        $p = new Process('php tests/artisan ' . $cmd . ' sleep=2');
        $p->start();

        sleep(1);
        $this->assertTrue(file_exists($pid)); // still sleeping

        sleep(3);
        $this->assertTrue(! file_exists($pid)); // aweken and terminated
    }
}
