JsonRpc Server and Client for Yii2


##Usage Server

1) Install with Composer

~~~php
"require": {
    "unlimix/yii2-json-rpc": "dev-master",
},

php composer.phar update
~~~

2) Add action to controller

~~~php
public function actions()
{
    return array(
        'index' => array(
            'class' => '\unlimix\jsonRpc\Action',
        ),
    );
}

public function sum($a, $b) {
	return $a + $b;
}
~~~

3) TEST:

~~~php
function sendRPC(){
		$.ajax({
			url: 'YOUR URL',
			data: JSON.stringify({
				"jsonrpc": "2.0",
				"id": '<?php echo md5(microtime()); ?>',
				"method": "sum",
				"params": [1, 2]
			}),
			type: 'POST',
			dataType: 'JSON',
			contentType: 'application/json-rpc',
			complete: function (xhr, status) {
				console.log(xhr);
				console.log(status);
			}
		});
	}
~~~

4) Enjoy!



