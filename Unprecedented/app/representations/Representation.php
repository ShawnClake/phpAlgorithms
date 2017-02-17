<?php namespace App\Representations;

use App\Classes\Meta;

/**
 * Class Representation
 * @method static Representation make(array $file_contents, $separator = '===')
 * @package App\Representations
 */
abstract class Representation
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
    public $settings = [];

    /**
     * PHP code stored inside of a page/layout
     * @var array
     */
    public $php = [];

    /**
     * Representation Markup
     * @var array
     */
    public $content = [];

    /**
     * Separator string for splitting files into parts
     * @var string
     */
    public $separator;

    public function __construct($file_contents, $separator = "===")
    {
        //var_dump($file_contents);
        $this->parse($file_contents, $separator);
    }

    public function parse($file_contents, $separator = "===")
    {
        $this->separator = $separator;

        $this->uid = uniqid();

        $currentSection = 0;
        $section = [];

        foreach($file_contents as $line)
        {
            if (trim($line) === $this->separator)
            {
                $currentSection++;
                $this->separate($section, $currentSection);
            } else {
                $section[] = $line;
            }
        }

        $currentSection++;

        $this->separate($section, $currentSection);

        /*if($sections == 1)
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
        }*/

    }

    private function separate(&$section, $count)
    {
        switch($count)
        {
            case 1:
                $this->content = $section;
                $section = [];
                break;
            case 2:
                $this->settings = $this->content;
                $this->content = $section;
                $section = [];
                break;
            case 3:
                $this->php = $this->content;
                $this->content = $section;
                $section = [];
                break;
            default:
                break;
        }
    }

    public function getContentString()
    {
        if(empty($this->content))
            return null;
        return file_to_string($this->content);
    }

}