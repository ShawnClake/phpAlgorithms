<?php namespace App;

class Autoloader
{
    private $autoloaders = [
        // See composer.json for examples on how to do this
        //'Clake\\' => __DIR__ .
    ];

    /**
     * @var ../vendor/autoload.php
     */
    public $loader;

    public function __construct()
    {
        $this->loader = require __DIR__ . '/../vendor/autoload.php';
        foreach($this->autoloaders as $name=>$path)
        {
            $this->loader->add($name, $path);
        }
    }

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

    public function addProviders()
    {

    }
}