<?php namespace Clake\Core;

/**
 * Class Plugin
 *
 * The core plugin for Unprecedented
 *
 * Don't remove this plugin from the framework or your project under any circumstances. Doing so will cause the framework to break.
 *
 * @package Clake\Core
 */
class Plugin
{
    /**
     * First function called on a plugin.
     * Register the plugin's modules here
     */
    public function register()
    {

        Module::register();

    }

    /**
     * This function is called after all plugins and modules have been registered.
     */
    public function boot()
    {

    }

    public function testResponse()
    {
        echo 'Response found in plugin';
        return [null];
    }

    public function injectRouting()
    {
        return [
            'test.hi' => [
                'type' => 'GET',
                'handler' => 'testResponse'
            ],
        ];
    }

}