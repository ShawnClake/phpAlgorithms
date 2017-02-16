<?php namespace App\Theme;

class Theme
{
    public static function getSelectedTheme()
    {
        return new \Theme\Clake\Maintheme\Theme();
    }
}