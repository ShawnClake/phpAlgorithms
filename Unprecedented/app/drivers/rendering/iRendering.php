<?php namespace App\Drivers\Rendering;

interface iRendering
{
    public function initializeFactory();

    public function extend();

    public function render($name, array $param = []);
}