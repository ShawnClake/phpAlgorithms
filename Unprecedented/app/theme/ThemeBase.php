<?php namespace App\Theme;

use App\Classes\Meta;

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

    public $theme_root;

    public static $layouts;

    public static $pages;

    public static $partials;

    public static $snippets;

    public function __construct()
    {
        $reflection = new \ReflectionClass($this);
        $this->path = $reflection->getNamespaceName();
    }

    public function generateListings()
    {
        $this->theme_root = path_root('/' . $this->path);

        if(empty(self::$layouts))
            $this->generateLayoutListings($this->theme_root);

    }

    public function generateLayoutListings($theme_root)
    {
        $layouts = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($theme_root . '/layouts', \RecursiveDirectoryIterator::SKIP_DOTS));
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

    public function generatePageListings($theme_root)
    {
        $pages = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($theme_root . '/pages', \RecursiveDirectoryIterator::SKIP_DOTS));
        foreach($pages as $name => $page)
        {
            self::$pages[] = substr($name, strlen($theme_root . '/pages') + 1);
        }
    }
    
    public function generatePartialListings($theme_root)
    {
        $partials = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($theme_root . '/partials', \RecursiveDirectoryIterator::SKIP_DOTS));
        foreach($partials as $name => $partial)
        {
            self::$partials[] = substr($name, strlen($theme_root . '/partials') + 1);
        }
    }
    
    public function generateSnippetListings($theme_root)
    {
        $snippets = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($theme_root . '/snippets', \RecursiveDirectoryIterator::SKIP_DOTS));
        foreach($snippets as $name => $snippet)
        {
            self::$snippets[] = substr($name, strlen($theme_root . '/snippets') + 1);
        }
    }

    public function getLayout($layout)
    {
        if(!in_array($layout, self::$layouts))
            return null;

        return file($this->theme_root . '/layouts/' . $layout);
    }

    public function getPage($page)
    {
        if(!in_array($page, self::$pages))
            return null;

        return file($this->theme_root . '/pages/' . $page);
    }

    public function getPartial($partial)
    {
        if(!in_array($partial, self::$partials))
            return null;

        return file($this->theme_root . '/partials/' . $partial);
    }

    public function getSnippet($snippet)
    {
        if(!in_array($snippet, self::$snippets))
            return null;

        return file($this->theme_root . '/snippets/' . $snippet);
    }

}