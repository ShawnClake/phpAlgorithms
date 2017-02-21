<?php namespace App\Classes;

use App\App;
use App\Drivers\Rendering\Markdown;
use App\Drivers\Rendering\ParseDown;
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

    /**
     * @var ParseDown
     */
    public static $markdown;

    public function makeFactory(Page $page)
    {
        self::$page = $page;

        App::$theme->generateListings();

        App::$theme->generateRepresentations();

        self::$markdown = ParseDown::register()->extend();

        self::$twig = Twig::register()->extend();

        self::$page->getPathToPage();

        return $this;
    }

    public function render()
    {
        if(isset(self::$page->representation))
        {
            self::$page->content = $this->preprocessor();

            self::$page->content = $this->assembler();
            //var_dump(self::$page->content);
            self::$page->content = $this->processor();
        } else {
            self::$page->content = $this->routeNotFound();
        }

        echo self::$page->content;
    }

    protected function assembler()
    {
        self::$twig->boot();
        //echo self::$page->representation->path_file;
        return self::$twig->render('pages.' . self::$page->representation->path_file, ['name' => false]);
    }

    protected function preprocessor()
    {
        self::$markdown->boot();
        return self::$markdown->render(self::$page->content);
    }

    protected function processor()
    {
        return self::$page->content;
        //return self::$markdown->render($content);
    }

    protected function routeNotFound()
    {
        return 'Error 404 - Route not found';
    }

}