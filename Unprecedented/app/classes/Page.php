<?php namespace App\Classes;

use App\App;
use App\Representations\Representation;
use App\StaticFactory;
use App\Theme\ThemeBase;

/**
 * Class Page
 * @method static Page make($routing)
 * @package App\Classes
 */
class Page extends StaticFactory
{
    /** @var string This is the resolved URI */
    public $routing;

    public $content;

    /** @var Representation */
    public $representation = null;

    public function makeFactory($routing)
    {
        $this->routing = $routing;
        return $this;
    }

    public function getPathToPage()
    {
        $pages = ThemeBase::$pages;
        foreach($pages as $page)
        {
            //var_dump($contents);
            $page = new \App\Representations\Theme\Page(App::$theme->theme_root . '/pages/', $page);
            //echo $this->routing == trim($page->getSetting('route'));
            //var_dump($page->getSetting('route'));
            if($this->routing == trim($page->getSetting('route')))
            {
                $this->representation = $page;
            }

        }
    }

}