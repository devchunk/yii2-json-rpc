<?php

namespace devchunk\jsonRpc;

use Yii;
use ReflectionClass;
use yii\helpers\Json;
use yii\web\HttpException;
use devchunk\jsonRpc\Exception;
use yii\web\Response;

class Action extends \yii\base\Action {

    protected $_request = NULL;
    protected $_output = NULL;

    public function run() {
        $this->failIfNotAJsonRpcRequest();
        try {
            $this->_request = $this->getRequest();
            try {
                $this->_output = $this->tryToRunMethod();
            } catch (Exception $e) {
                throw new Exception($e->getMessage(), Exception::INTERNAL_ERROR);
            }

            $this->answer();
        } catch (Exception $e) {
            $this->answer($e);
        }
    }

    protected function answer($exception = null) {
        $answer = array(
            'jsonrpc' => '2.0',
            'id' => isset($this->_request['id']) ? $this->_request['id'] : null,
        );
        if ($exception) {
            $answer['error'] = $exception->getErrorAsArray();
        }
        if ($this->_output) {
            $answer['result'] = $this->_output;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        echo Json::encode($answer);
    }

    protected function getHandler() {
        return $this->getHandlerClass()->getMethod($this->_request['method']);
    }

    protected function getHandlerClass() {
        return new ReflectionClass($this->controller);
    }

    protected function runMethod($method, $params) {
        return $method->invokeArgs($this->controller, $params);
    }

    protected function tryToRunMethod() {
        $method = $this->getHandler();
        $class = $this->getHandlerClass();

        $result = $this->runMethod($method, isset($this->_request['params']) ? $this->_request['params'] : null);

        if (!$class->hasMethod($this->_request['method']))
            throw new Exception("Method not found", Exception::METHOD_NOT_FOUND);

        return $result;
    }

    protected function failIfNotAJsonRpcRequest() {
        if (!Yii::$app->request->getIsPost() ||
            empty(Yii::$app->request->getContentType()) ||
            !preg_match('/^application\/json\-rpc/',
                Yii::$app->request->getContentType())
        )
            throw new HttpException(404, "Page not found");
    }

    protected function getRequest() {
        $request = json_decode(file_get_contents('php://input'), true);
        if (!$this->isValidRequest($request)) {
            throw new Exception("Invalid Request", Exception::INVALID_REQUEST);
        }

        return $request;
    }

    protected function isValidRequest($request) {
        return isset($request['jsonrpc'])
        && $request['jsonrpc'] == '2.0' && isset($request['method']);
    }

}
