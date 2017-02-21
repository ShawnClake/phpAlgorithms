<?php namespace App\Drivers\Rendering;

/**
 * Interface iRendering
 * @package App\Drivers\Rendering
 */
interface iRendering
{
    /**
     * Registers the renderer
     * @return $this
     */
    public function registerFactory();

    /**
     * Extends the renderer if applicable
     * @return $this
     */
    public function extend();

    /**
     * Boots the renderer after being registered and extended
     * @return $this
     */
    public function boot();

    /**
     * Preforms the render
     * @param string $content
     * @param array $param
     * @return mixed
     */
    public function render($content = '', array $param = []);
}