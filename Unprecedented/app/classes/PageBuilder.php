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

        self::$page->getPathToPage();

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
        //echo self::$page->representation->path_file;
        if(isset(self::$page->representation))
            return self::$twig->render('pages.' . self::$page->representation->path_file, ['name' => false]);
        else
            return 'Error 404 - Route not found';
    }

    public function processor()
    {
        return self::$page->content;
    }

}