<?php namespace App\Classes;

use App\App;
use App\Drivers\Rendering\Twig;
use App\StaticFactory;

/**
 * Class PageBuilder
 * @method static PageBuilder make(Page &$page)
 * @package App\Classes
 */
class PageBuilder extends StaticFactory
{
    /**
     * @var Page
     */
    public static $page;

    /**
     * @var Twig
     */
    public static $twig;

    public static $parse_down;

    public function makeFactory(Page $page)
    {
        self::$page = $page;

        App::$theme->generateListings();

        self::$twig = Twig::initialize()->extend();

        return $this;
    }

    public function render()
    {
        self::$page->content = $this->assembler();
        self::$page->content = $this->processor();
        echo self::$page->content;
    }

    public function assembler()
    {
        return self::$twig->render('layouts/main.htm');
    }

    public function processor()
    {
        return self::$page->content;
    }

}