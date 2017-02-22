<?php namespace App\Drivers\Rendering;

use App\Drivers\Rendering;
use App\App;
use App\StaticFactoryTrait;

/**
 * Class ParseDown
 * @method static ParseDown register()
 * @package App\Drivers\Rendering
 */
class ParseDown extends Rendering
{
    use StaticFactoryTrait;

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
     * Converts a representation's markdown syntax if its marked as md in the settings
     * Utilizes a call back to do so.
     * @param $content_not_used
     * @param array $param
     * @return mixed|string
     */
    public function render($content_not_used = '', array $param = ['breaks' => true, 'escaped' => false, 'autolinks' => true])
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