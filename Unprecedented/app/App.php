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

    public function makeFactory()
    {
        $this->kernel = Kernel::init();
        $this->kernel->register();
        $this->kernel->boot();
    }

}