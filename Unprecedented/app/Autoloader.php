<?php namespace App;

/**
 * Class Autoloader
 * The application autoloader
 * Utilizes Composers existing auto loading framework and then extends it by adding plugins, providers etc.
 * @package App
 */
class Autoloader
{
    /**
     * Additional auto loader providers to add in
     * @var array
     */
    private $autoloaders = [
        // See composer.json for examples on how to do this
        //'Clake\\' => __DIR__
    ];

    /**
     * @var ../vendor/autoload.php
     */
    public $loader;

    /**
     * Autoloader constructor.
     * Creates the composer autoloader and adds in the preliminary extra autoloader providers.
     */
    public function __construct()
    {
        $this->loader = require __DIR__ . '/../vendor/autoload.php';
        foreach($this->autoloaders as $name=>$path)
        {
            $this->loader->add($name, $path);
        }
    }

    /**
     * Adds each author as a separate autoloader provider
     * @return $this
     */
    public function addPlugins()
    {
        $authors = array_diff(scandir('plugins'), ['..', '.']);
        foreach($authors as $author)
        {
            if(!is_file($author))
            {
                $this->loader->addPsr4(ucfirst($author) . '\\', 'plugins/' . $author . '/');
            }
        }
        return $this;
    }

    /**
     * TODO: Adds additional autoloader providers from a config
     */
    public function addProviders()
    {

    }
}