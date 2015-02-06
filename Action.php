<?php
namespace nizsheanez\jsonRpc;

use Yii;
use ReflectionClass;
use ReflectionMethod;
use yii\web\HttpException;
use nizsheanez\jsonRpc\Exception;

class Action extends \yii\base\Action
{
    use traits\Serializable;

    public function run()
    {
        $this->failIfNotAJsonRpcRequest();
        Yii::beginProfile('service.request');
        $output = null;
        try {
            $this->setRequestMessage(file_get_contents('php://input'));
            $this->result = $this->tryToRunMethod();
        } catch (Exception $e) {
            Yii::error($e, 'service.error');
            $this->exception = new Exception($e->getMessage(), Exception::INTERNAL_ERROR);
        }
        echo $this->toJson();
        Yii::endProfile('service.request');
    }

    /**
     * @return string|callable|ReflectionMethod
     */
    protected function getHandler()
    {
        $class = new ReflectionClass($this->controller);
        if (!$class->hasMethod($this->getMethod())) {
            throw new Exception("Method not found", Exception::METHOD_NOT_FOUND);
        }
        $method = $class->getMethod($this->getMethod());

        return $method;
    }

    /**
     * @param string|callable|\ReflectionMethod $method
     * @param array $params
     *
     * @return mixed
     */
    protected function runMethod($method, $params)
    {
        return $method->invokeArgs($this->controller, $params);
    }

    protected function tryToRunMethod()
    {
        $method = $this->getHandler();
        Yii::beginProfile('service.request.action');
        $output = $this->runMethod($method, $this->getParams($method));
        Yii::endProfile('service.request.action');
        Yii::info($method, 'service.output');
        Yii::info($output, 'service.output');

        return $output;
    }

    protected function failIfNotAJsonRpcRequest()
    {
        if (!Yii::$app->request->isPost || !$this->checkContentType()) {
            throw new HttpException(404, "Page not found");
        }
    }
}