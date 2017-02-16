<?php namespace App\Representations;

use App\Classes\Meta;
use App\StaticFactory;

/**
 * Class Representation
 * @method static Representation make($file_contents, $separator = '===')
 * @package App\Representations
 */
abstract class Representation extends StaticFactory
{
    /**
     * Meta data
     * @var Meta
     */
    public $meta;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var array
     */
    public $settings;

    /**
     * PHP code stored inside of a page/layout
     * @var string
     */
    public $php;

    /**
     * Representation Markup
     * @var string
     */
    public $content;

    /**
     * Separator string for splitting files into parts
     * @var string
     */
    public $separator;

    public function makeFactory($file_contents, $separator = '===')
    {
        $this->separator = $separator;

        $this->uid = uniqid();

        $sections = substr_count($file_contents, $this->separator) + 1;

        if($sections == 1)
        {
            $this->content = $file_contents;
            return $this;
        }

        if($sections == 2)
        {
            $sep1 = strpos($file_contents, $this->separator);
            $this->settings = substr($file_contents, 0, $sep1);
            $this->content = substr($file_contents, $sep1 + 3);
            return $this;
        }

        if($sections == 3)
        {
            $sep1 = strpos($file_contents, $this->separator);
            $sep2 = strpos($file_contents, $this->separator, $sep1);
            $this->settings = substr($file_contents, 0, $sep1);
            $this->php = substr($file_contents, $sep1 + 3, $sep2);
            $this->content = substr($file_contents, $sep2 + 3);
            return $this;
        }

        return $this;
    }

}