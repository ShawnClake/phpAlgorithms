<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class RoutingResponse
 * @method static RoutingResponse make($response, $type)
 * @package App\Classes
 */
class RoutingResponse extends StaticFactory
{
    /** @var string */
    public $type;

    /** @var mixed */
    public $response;

    /**
     * Factory
     * @param $response
     * @param $type
     * @return $this
     */
    public function makeFactory($response, $type)
    {
        $this->response = $response;
        $this->type = $type;
        return $this;
    }

    /**
     * If the response is of type PageBuilder, then it'll render the response
     * @return mixed
     */
    public function render()
    {
        if($this->type == 'page' && $this->response instanceof PageBuilder)
        {
            /** @var $this->response PageBuilder */
            return $this->response->render();
        }
    }

}