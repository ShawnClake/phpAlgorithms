<?php namespace App\Drivers\Rendering;

use App\App;
use App\StaticFactory;

/**
 * Class Twig
 * @method static Twig initialize()
 * @package App\Drivers\Rendering
 */
class Twig extends StaticFactory implements iRendering
{

    /**
     * @var \Twig_Loader_Chain
     */
    public static $twig_loader;

    /**
     * @var \Twig_Environment
     */
    public static $twig_environment;

    public function initializeFactory()
    {
        $layouts = new \Twig_Loader_Array(App::$theme->layoutsToTwig());
        $pages = new \Twig_Loader_Array(App::$theme->pagesToTwig());
        $partials = new \Twig_Loader_Array(App::$theme->partialsToTwig());
        $snippets = new \Twig_Loader_Array(App::$theme->snippetsToTwig());

        //echo json_encode(App::$theme->layoutsToTwig());

        self::$twig_loader = new \Twig_Loader_Chain([$layouts, $pages, $partials, $snippets]);

        self::$twig_environment = new \Twig_Environment(self::$twig_loader);

        return $this;
    }

    public function extend()
    {
        // TODO: Implement extend() method.
        return $this;
    }

    /**
     * @param $name
     * @param array $params
     * @return string
     */
    public function render($name, array $params = [])
    {
        echo '<br>';
        echo $name . '<br>';
        // TODO: Implement render() method.
        return self::$twig_environment->render($name, $params);
    }
}