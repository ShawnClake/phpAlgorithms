<?php namespace App\Theme;

/**
 * Class Theme
 * @package App\Theme
 */
class Theme
{
    /**
     * TODO: Have a theme selector a not a hard coded entry
     * @return \Theme\Clake\Maintheme\Theme
     */
    public static function getSelectedTheme()
    {
        return new \Theme\Clake\Maintheme\Theme();
    }
}