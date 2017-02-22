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

    /** @var string */
    public $content;

    /** @var Representation */
    public $representation = null;

    /** @var bool */
    public $cached = false;

    /** @var string */
    public $md5 = '';

    /**
     * Factory
     * @param $routing
     * @return $this
     */
    public function makeFactory($routing)
    {
        $this->routing = $routing;
        return $this;
    }

    /**
     * Creates a page representation for the resolved URI we are trying to load.
     * Takes the
     */
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

    public function retrieveIfCached()
    {
        if(isset(App::$kernel->cache) && App::$kernel->cache->isCached('compiled_pages', $this->routing, file(fix_slashes($this->representation->path . $this->representation->path_file))))
        {
            $this->cached = true;
            $this->content = file_to_string(App::$kernel->cache->retrieve('compiled_pages', $this->routing));
            return true;
        }

        return false;
    }

    public function cache()
    {
        if(App::$kernel->cache)
            App::$kernel->cache->store('compiled_pages', $this->routing, $this->content, md5(file_to_string(file(fix_slashes($this->representation->path . $this->representation->path_file)))));
    }


}