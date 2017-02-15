<?php namespace App;

/**
 * Class App
 * @method static App make()
 * @package App
 */
class App extends StaticFactory
{

    /**
     * @var \App\Kernel
     */
    public $kernel;

    public $helpers;

    public $autoloader;

    public function makeFactory()
    {
        $this->exposeHelpers();
        $this->kernelStartup();
        return $this;
    }

    public function exposeHelpers()
    {
        $this->helpers = Helpers::expose();
    }

    public function kernelStartup()
    {
        $this->kernel = Kernel::init();
        $this->kernel->register();
        $this->kernel->boot();
    }

    public function route()
    {
        $route = $this->kernel->route();
    }

}