<?php namespace App\Drivers;

use App\Drivers\Rendering\iRendering;

abstract class Rendering implements iRendering
{

    /**
     * @return mixed
     */
    public function registerFactory()
    {
        // TODO: Implement registerFactory() method.
    }

    /**
     * @return mixed
     */
    public function extend()
    {
        // TODO: Implement extend() method.
    }

    /**
     * @return mixed
     */
    public function boot()
    {
        // TODO: Implement boot() method.
    }

    /**
     * @param string $content
     * @param array $param
     * @return mixed
     */
    public function render($content = '', array $param = [])
    {
        // TODO: Implement render() method.
    }
}