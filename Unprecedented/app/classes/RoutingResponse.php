<?php namespace App\Classes;

use App\StaticFactory;

/**
 * Class RoutingResponse
 * @method static RoutingResponse make($response, $type)
 * @package App\Classes
 */
class RoutingResponse extends StaticFactory
{
    public $type;

    public $response;

    public function makeFactory($response, $type)
    {
        $this->response = $response;
        $this->type = $type;
        return $this;
    }

    public function render()
    {
        if($this->type == 'page' && $this->response instanceof PageBuilder)
        {
            /** @var $this->response PageBuilder */
            return $this->response->render();
        }
    }

}