<?php

namespace LaravelSingleInstanceCommand;

/**
 * Laravel single instance command service provider
 *
 * @author Denis Milovanov <me@denismilovanov.net>
 */

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->commands('LaravelSingleInstanceCommand\StopAllCommand');
        $this->commands('LaravelSingleInstanceCommand\RemovePidsCommand');
        $this->commands('LaravelSingleInstanceCommand\TestSingleInstanceCommand');
    }

    public function provides()
    {
        return [
            'LaravelSingleInstanceCommand\LaravelSingleInstanceCommand',
        ];
    }
}
