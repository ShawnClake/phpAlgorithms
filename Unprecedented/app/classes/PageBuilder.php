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

    /**
     * Factory
     * @param Page $page
     * @return $this
     */
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

    /**
     * Renders the page via the theme
     */
    public function render()
    {
        if(isset(self::$page->representation))
        {
            if(!self::$page->retrieveIfCached())
            {
                self::$page->content = $this->preprocessor();

                self::$page->content = $this->assembler();
                //var_dump(self::$page->content);
                self::$page->content = $this->processor();

                self::$page->cache();
            }
        } else {
            self::$page->content = $this->routeNotFound();
        }

        echo self::$page->content;
    }

    /**
     * Assembles various Representations into a complete page
     * @return string
     */
    protected function assembler()
    {
        self::$twig->boot();
        //echo self::$page->representation->path_file;
        return self::$twig->render('pages.' . self::$page->representation->path_file, ['name' => false]);
    }

    /**
     * Pre-processes Representations prior to assembly
     * @return mixed|string
     */
    protected function preprocessor()
    {
        self::$markdown->boot();
        return self::$markdown->render(self::$page->content);
    }

    /**
     * Processes the assembled page. Think of this as a post-process
     * @return string
     */
    protected function processor()
    {
        return self::$page->content;
        //return self::$markdown->render($content);
    }

    /**
     * Handles encountering a route with no applicable destination
     * @return string
     */
    protected function routeNotFound()
    {
        return 'Error 404 - Route not found';
    }

}