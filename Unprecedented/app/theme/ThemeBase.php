<?php namespace App\Theme;

use App\Classes\Meta;
use App\Representations\Theme\Layout;
use App\Representations\Theme\Page;
use App\Representations\Theme\Partial;
use App\Representations\Theme\Snippet;

/**
 * Class ThemeBase
 * @method static ThemeBase make()
 * @package App\Theme
 */
abstract class ThemeBase
{
    /**
     * @var Meta
     */
    public $meta;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $theme_root;

    /**
     * @var string[]
     */
    public static $layouts;

    /**
     * @var string[]
     */
    public static $pages;

    /**
     * @var string[]
     */
    public static $partials;

    /**
     * @var string[]
     */
    public static $snippets;

    /**
     * ThemeBase constructor.
     */
    public function __construct()
    {
        $reflection = new \ReflectionClass($this);
        $this->path = $reflection->getNamespaceName();
        $this->theme_root = path_root('/' . $this->path);
        /*
         * I don't want to call generateListings here because this is built on app load. We don't
         * necessarily need the listings for API/plugin/module overrides. Wait for PageBuilder to call generateListings
         */
    }

    /**
     *
     */
    public function generateListings()
    {
        if(empty(self::$layouts))
            $this->generateLayoutListings($this->theme_root);

        if(empty(self::$pages))
            $this->generatePageListings($this->theme_root);

        if(empty(self::$partials))
            $this->generatePartialListings($this->theme_root);

        if(empty(self::$snippets))
            $this->generateSnippetListings($this->theme_root);
    }

    /**
     * @param $theme_root string
     */
    private function generateLayoutListings($theme_root)
    {
        $layouts = get_files_recursively($theme_root . '/layouts');
        //$layouts = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($theme_root . '/layouts', \RecursiveDirectoryIterator::SKIP_DOTS));
        foreach($layouts as $name => $layout)
        {
            self::$layouts[] = substr($name, strlen($theme_root . '/layouts') + 1);
            //echo "$name\n";
            //echo json_encode(get_class_methods($layout)) . "\n";
            //echo $layout->getPath() . '<br><br>';
            //echo json_encode(file($name));
        }

        //self::$layouts = array_diff(scandir($theme_root . '/layouts'), ['..', '.']);
    }

    /**
     * @param $theme_root string
     */
    private function generatePageListings($theme_root)
    {
        $pages = get_files_recursively($theme_root . '/pages');
        foreach($pages as $name => $page)
        {
            self::$pages[] = substr($name, strlen($theme_root . '/pages') + 1);
        }
    }

    /**
     * @param $theme_root string
     */
    private function generatePartialListings($theme_root)
    {
        $partials = get_files_recursively($theme_root . '/partials');
        foreach($partials as $name => $partial)
        {
            self::$partials[] = substr($name, strlen($theme_root . '/partials') + 1);
        }
    }

    /**
     * @param $theme_root string
     */
    private function generateSnippetListings($theme_root)
    {
        $snippets = get_files_recursively($theme_root . '/snippets');
        foreach($snippets as $name => $snippet)
        {
            self::$snippets[] = substr($name, strlen($theme_root . '/snippets') + 1);
        }
    }

    /**
     * @param $layout
     * @return Layout
     */
    public function getLayout($layout)
    {
        if(!in_array($layout, self::$layouts))
            return null;

        $contents = file($this->theme_root . '/layouts/' . $layout);

        //var_dump(file($this->theme_root . '/layouts/' . $layout));

        return new Layout($contents);
        //return file($this->theme_root . '/layouts/' . $layout);
    }

    /**
     * @param $page
     * @return Page
     */
    public function getPage($page)
    {
        if(!in_array($page, self::$pages))
            return null;

        $contents = file($this->theme_root . '/pages/' . $page);

        return new Page($contents);
        //return file($this->theme_root . '/pages/' . $page);
    }

    /**
     * @param $partial
     * @return Partial
     */
    public function getPartial($partial)
    {
        if(!in_array($partial, self::$partials))
            return null;

        $contents = file($this->theme_root . '/partials/' . $partial);

        return new Partial($contents);
        //return file($this->theme_root . '/partials/' . $partial);
    }

    /**
     * @param $snippet
     * @return Snippet
     */
    public function getSnippet($snippet)
    {
        if(!in_array($snippet, self::$snippets))
            return null;

        $contents = file($this->theme_root . '/snippets/' . $snippet);

        return new Snippet($contents);
        //return file($this->theme_root . '/snippets/' . $snippet);
    }

    public function layoutsToTwig()
    {
        if(empty(self::$layouts))
            return [];

        $layouts = [];
        foreach(self::$layouts as $layout)
        {
            $layouts['layouts.' . $layout] = $this->getLayout($layout)->getContentString();
        }
        return $layouts;
    }

    public function pagesToTwig()
    {
        if(empty(self::$pages))
            return [];

        $pages = [];
        foreach(self::$pages as $page)
        {
            $pages['pages.' . $page] = $this->getPage($page)->getContentString();
        }
        return $pages;
    }

    public function partialsToTwig()
    {
        if(empty(self::$partials))
            return [];

        $partials = [];
        foreach(self::$partials as $partial)
        {
            $partials['partials.' . $partial] = $this->getPartial($partial)->getContentString();
        }
        return $partials;
    }

    public function snippetsToTwig()
    {
        if(empty(self::$snippets))
            return [];

        $snippets = [];
        foreach(self::$snippets as $snippet)
        {
            $snippets['snippets.' . $snippet] = $this->getSnippet($snippet)->getContentString();
        }
        return $snippets;
    }

}