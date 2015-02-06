<?php 
namespace nizsheanez\jsonRpc;

class Client
{
    use traits\Client;

    protected $url;

    public function __construct($url = null)
    {
        $this->url = $url;
    }

    public function __call($name, $arguments)
    {
        return $this->callServer($name, $arguments, $this->url);
    }
}
