<?php

namespace App\Drivers\Rendering;

use App\App;
use App\StaticFactory;
use cebe\markdown\GithubMarkdown;
use cebe\markdown\MarkdownExtra;

/**
 * Class Markdown
 * @method static Markdown register()
 * @package App\Drivers\Rendering
 */
class Markdown extends StaticFactory implements iRendering
{
    /** @var GithubMarkdown */
    public static $markdown;

    /**
     * @return mixed
     */
    public function registerFactory()
    {
        self::$markdown = new MarkdownExtra();
        return $this;
    }

    /**
     * @return mixed
     */
    public function extend()
    {
        // TODO: Implement extend() method.
        return $this;
    }

    /**
     * Converts a representation's markdown syntax if its marked as md in the settings
     * Utilizes a call back to do so.
     * @param $content_not_used
     * @param array $param
     * @return string
     */
    public function render($content_not_used = '', array $param = ['html5' => true, 'listnumbering' => true, 'newlines' => true])
    {
        self::$markdown->html5 = $param['html5'];
        self::$markdown->keepListStartNumber = $param['listnumbering'];
        self::$markdown->enableNewlines = $param['newlines'];

        $parser = self::$markdown;

        $callback = function($content) use ($parser) { return $parser->parse($content); };

        App::$theme->modifyRepresentations($callback);

        return '';
    }

    /**
     * @return mixed
     */
    public function boot()
    {
        // TODO: Implement boot() method.
        return $this;
    }
}