<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class PageBuilder
 * @method static PageBuilder make(Page &$page)
 * @package App\Classes
 */
class PageBuilder extends StaticFactory
{
    public static $layouts;

    public static $pages;

    public static $partials;

    public static $snippets;

    public static $page;

    public function makeFactory(Page $page)
    {
        self::$page = $page;

        if(empty(self::$layouts))
            $this->generateLayoutListings();

        return $this;
    }

    public function generateLayoutListings()
    {
        echo 'Response built in PageBuilder';
    }
}