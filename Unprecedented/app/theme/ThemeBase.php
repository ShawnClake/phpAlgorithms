<?php namespace App\Theme;

use App\Classes\Meta;
use App\Drivers\Rendering\Markdown;
use App\Representations\Representation;
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
    public static $layouts = [];

    /**
     * @var string[]
     */
    public static $pages = [];

    /**
     * @var string[]
     */
    public static $partials = [];

    /**
     * @var string[]
     */
    public static $snippets = [];

    /**
     * @var Representation[string][string]
     */
    public static $representations = [];

    abstract public function register();

    /**
     * ThemeBase constructor.
     */
    public function __construct()
    {
        $reflection = new \ReflectionClass($this);
        $this->path = $reflection->getNamespaceName();
        $this->theme_root = path_root('/' . $this->path);
        $this->meta = new Meta();
        $this->registrationHandler($this->register());
        /*
         * I don't want to call generateListings here because this is built on app load. We don't
         * necessarily need the listings for API/plugin/module overrides. Wait for PageBuilder to call generateListings
         */
    }

    private function registrationHandler(array $metas)
    {
        foreach($metas as $key=>$meta)
        {
            $this->meta->$key = $meta;
        }
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

        //var_dump(file($this->theme_root . '/layouts/' . $layout));

        return new Layout($this->theme_root . '/layouts/', $layout);
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

        return new Page($this->theme_root . '/pages/', $page);
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

        return new Partial($this->theme_root . '/partials/', $partial);
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

        return new Snippet($this->theme_root . '/snippets/', $snippet);
        //return file($this->theme_root . '/snippets/' . $snippet);
    }

    public function layoutsToTwig()
    {
        if(empty(self::$layouts))
            return [];

        $layouts = [];
        foreach(self::$representations['layouts'] as $key=>$layout)
        {
            $layouts['layouts.' . path_to_dot($key)] = $layout->getContentString();
        }
        return $layouts;
    }

    public function pagesToTwig()
    {
        if(empty(self::$pages))
            return [];

        $pages = [];
        foreach(self::$representations['pages'] as $key=>$page)
        {
            $pages['pages.' . path_to_dot($key)] = $page->getContentString();
        }
        return $pages;
    }

    public function partialsToTwig()
    {
        if(empty(self::$partials))
            return [];

        $partials = [];
        foreach(self::$representations['partials'] as $key=>$partial)
        {
            $partials['partials.' . path_to_dot($key)] = $partial->getContentString();
        }
        return $partials;
    }

    public function snippetsToTwig()
    {
        if(empty(self::$snippets))
            return [];

        $snippets = [];
        foreach(self::$representations['snippets'] as $key=>$snippet)
        {
            $snippets['snippets.' . path_to_dot($key)] = $snippet->getContentString();
        }
        return $snippets;
    }

    /**
     * @return Representation
     */
    public function generateRepresentations()
    {
        if(!empty(self::$layouts))
        {
            foreach(self::$layouts as $layout)
            {
                self::$representations['layouts'][$layout] = $this->getLayout($layout);
            }
        }

        if(!empty(self::$pages))
        {
            foreach(self::$pages as $page)
            {
                $representation = $this->getPage($page);

                $layout = $representation->getSetting('layout');

                if(empty($layout))
                    continue;

                self::$representations['pages'][$page] = $representation;
            }
        }

        if(!empty(self::$partials))
        {
            foreach(self::$partials as $partial)
            {
                self::$representations['partials'][$partial] = $this->getPartial($partial);
            }
        }

        if(!empty(self::$snippets))
        {
            foreach(self::$snippets as $snippet)
            {
                self::$representations['snippets'][$snippet] = $this->getSnippet($snippet);
            }
        }

        return self::$representations;
    }

    /**
     * @param $callback
     */
    public function modifyRepresentations($callback)
    {
        if(!empty(self::$layouts))
        {
            foreach (self::$representations['layouts'] as $key => $layout)
            {
                if($layout->getSetting('md'))
                    self::$representations['layouts'][$key]->content_string = $callback($layout->getContentString());
            }
        }

        if(!empty(self::$pages))
        {
            foreach (self::$representations['pages'] as $key => $page)
            {
                $content_string = $page->getContentString();

                if($page->getSetting('md'))
                {
                    $content_string = $callback($content_string);
                }

                $layout = $page->getSetting('layout');

                $content_string = "{% extends '" . trim($layout) . "' %} \n {% block page %} \n " . $content_string . " \n {% endblock %}";
                self::$representations['pages'][$key]->content_string = $content_string;
            }
        }

        if(!empty(self::$partials))
        {
            foreach (self::$representations['partials'] as $key => $partial)
            {
                if($partial->getSetting('md'))
                    self::$representations['partials'][$key]->content_string = $callback($partial->getContentString());
            }
        }

        if(!empty(self::$snippets))
        {
            foreach (self::$representations['snippets'] as $key => $snippet)
            {
                if($snippet->getSetting('md'))
                    self::$representations['snippets'][$key]->content_string = $callback($snippet->getContentString());
            }
        }
    }

}