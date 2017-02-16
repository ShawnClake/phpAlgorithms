<?php namespace App\Theme;

use App\Classes\Meta;

/**
 * Class ThemeBase
 * @method static ThemeBase make()
 * @package App\Theme
 */
abstract class ThemeBase
{
    /**
     * @var Meta
     */
    public $meta;

    /**
     * @var string
     */
    public $path;

    public function __construct()
    {
        $reflection = new \ReflectionClass($this);
        $this->path = $reflection->getNamespaceName();
    }

}