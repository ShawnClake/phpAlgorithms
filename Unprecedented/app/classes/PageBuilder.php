<?php namespace App\Classes;

use App\App;
use App\StaticFactory;

/**
 * Class PageBuilder
 * @method static PageBuilder make(Page &$page)
 * @package App\Classes
 */
class PageBuilder extends StaticFactory
{
    public static $page;

    public function makeFactory(Page $page)
    {
        self::$page = $page;

        App::$theme->generateListings();

        return $this;
    }

}