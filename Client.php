<?php
namespace devchunk\jsonRpc;

use devchunk\jsonRpc\Exception;

class Client {

    protected $url;
    protected $streamContext = [];

    public function __construct($url = null, $streamContext = []) {
        $this->url = $url;
        $this->streamContext = $streamContext;
    }

    public function __call($name, $arguments) {
        $id = md5(microtime());
        $request = [
            'jsonrpc' => '2.0',
            'method' => $name,
            'params' => $arguments,
            'id' => $id,
        ];

        $jsonRequest = json_encode($request);

        $ctx = stream_context_create(\yii\helpers\ArrayHelper::merge([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json-rpc\r\n",
                'content' => $jsonRequest
            ]
        ], $this->streamContext));

        $jsonResponse = file_get_contents($this->url, false, $ctx);

        if ($jsonResponse === '')
            throw new Exception('fopen failed', Exception::INTERNAL_ERROR);

        $response = json_decode($jsonResponse);

        if ($response === null)
            throw new Exception('JSON cannot be decoded', Exception::INTERNAL_ERROR);

        if ($response->id != $id)
            throw new Exception('Mismatched JSON-RPC IDs', Exception::INTERNAL_ERROR);

        if (property_exists($response, 'error'))
            throw new Exception($response->error->message, $response->error->code);
        else if (property_exists($response, 'result'))
            return $response->result;
        else
            throw new Exception('Invalid JSON-RPC response', Exception::INTERNAL_ERROR);
    }
}
