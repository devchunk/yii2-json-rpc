JsonRpc Server and Client for Yii2


##Usage Server

1) Install with Composer

~~~php
"require": {
    "devchunk/yii2-json-rpc": "dev-master",
},

php composer.phar update
~~~

2) Server:

Add action to controller (e.g. JsonRpcTestController)

~~~php
public function actions()
{
    return array(
        'index' => array(
            'class' => '\devchunk\jsonRpc\Action',
        ),
    );
}

public function echo($param) {
	return $param;
}
~~~

3) Client:

~~~php
$rpc = new Client('http://127.0.0.1/json-rpc-test', [
    'timeout' => 5,
]);

$result = $rpc->echo("some string");

echo sprintf("RPC result: %s\n", $r);
~~~


