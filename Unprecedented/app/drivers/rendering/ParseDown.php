<?php namespace App\Drivers\Rendering;

use App\StaticFactory;
use App\App;

/**
 * Class ParseDown
 * @method static ParseDown register()
 * @package App\Drivers\Rendering
 */
class ParseDown extends StaticFactory implements iRendering
{

    /** @var \ParsedownExtra */
    public static $parse_down;

    /**
     * @return mixed
     */
    public function registerFactory()
    {
        self::$parse_down = new \ParsedownExtra();
        return $this;
    }

    /**
     * @return mixed
     */
    public function extend()
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function boot()
    {
        // TODO: Implement boot() method.
        return $this;
    }

    /**
     * @param $content
     * @param array $param
     * @return mixed|string
     */
    public function render($content = '', array $param = ['breaks' => true, 'escaped' => false, 'autolinks' => true])
    {
        self::$parse_down->setBreaksEnabled($param['breaks']);
        self::$parse_down->setMarkupEscaped($param['escaped']);
        self::$parse_down->setUrlsLinked($param['autolinks']);

        $parser = self::$parse_down;

        $callback = function($content) use ($parser) { return $parser->text($content); };

        App::$theme->modifyRepresentations($callback);

        return '';
        //return self::$parse_down->text($content);
    }


}