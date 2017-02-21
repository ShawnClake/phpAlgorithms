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
     * @var string
     */
    public $content_string = '';

    /**
     * @var string
     */
    public $path = '';

    /**
     * @var string
     */
    public $path_file = '';

    /**
     * Separator string for splitting files into parts
     * @var string
     */
    public $separator;

    /**
     * Representation constructor.
     * @param $path_representation_root
     * @param $path_representation_file
     * @param string $separator
     */
    public function __construct($path_representation_root, $path_representation_file, $separator = "===")
    {
        //var_dump($file_contents);
        $this->path = $path_representation_root;
        $this->path_file = $path_representation_file;
        $this->uid = uniqid();
        $this->parse(file($this->path . $this->path_file), $separator);
    }

    /**
     * Parses through a file and separates the settings/php/content settings from each other.
     * @param $file_contents
     * @param string $separator
     */
    public function parse($file_contents, $separator = "===")
    {
        $this->separator = $separator;

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

        $this->buildSettings();

    }

    /**
     * Sets up a settings array based upon the settings found in a Representation
     */
    private function buildSettings()
    {
        $settings = [];
        foreach($this->settings as $key=>$setting)
        {
            $split = explode(': ', $setting);
            $option = $split[0];
            unset($split[0]);
            $value = implode($split);
            $settings[$option] = $value;
        }
        $this->settings = $settings;
    }

    /**
     * @param $section
     * @param $count
     */
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

    /**
     * Returns a setting
     * @param $setting
     * @return mixed|null
     */
    public function getSetting($setting)
    {
        if(isset($this->settings[$setting]))
            return $this->settings[$setting];
        return null;
    }

    /**
     * Converts the content of a file to a string, unless thats already been done, then just return the string
     * @return null|string
     */
    public function getContentString()
    {
        if(empty($this->content))
            return null;

        if(empty($this->content_string))
            $this->content_string = file_to_string($this->content);

        return $this->content_string;
    }

}