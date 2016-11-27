# LaravelSingleInstanceCommand for Laravel 5 (OBSOLETE)

This package allows you to save and control pids of your artisan commands in order to 
organize multiprocessing, support lengthy calculations, prevent more than one process run simultaneously, etc.

*I use this package only for backward compability.*

## Installation

    composer require 'denismilovanov/laravel-single-instance-command: 0.1.*@dev'

## Usage

In artisan:

    $app->register('LaravelSingleInstanceCommand\ServiceProvider');
    
In command:

    use \Symfony\Component\Console\Input\InputInterface;
    use \Symfony\Component\Console\Output\OutputInterface;
    
    class MyCommand extends \LaravelSingleInstanceCommand\Command
    {
        public $name = 'my-command';
         
        public function run(InputInterface $input, OutputInterface $output)
        {
            $this->checkInstance($input);
            
            // long living code here:
            // Queue::subscribe('queue', function ($job) {
            //
            // });
        }
    }
    
In shell (or crontab):

* to start process `php artisan my-command`,
* to start another process `php artisan my-command p=2`,
* to stop process `php artisan my-command p=2 stop`,
    
* to stop all `php artisan pids:stop-all`,
* to remove stale pids files `php artisan pids:remove`.

## Notes

Pids are save to /tmp/{APP_ENV}/ directory.  
Stopping process is performed by sending SIGTERM to it.
