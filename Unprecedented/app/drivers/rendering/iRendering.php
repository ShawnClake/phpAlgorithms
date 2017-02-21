<?php namespace App\Drivers\Rendering;

interface iRendering
{
    public function registerFactory();

    public function extend();

    public function boot();

    public function render($content = '', array $param = []);
}