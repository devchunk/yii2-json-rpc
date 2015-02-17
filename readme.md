JsonRpc Server and Client for Yii2


##Usage Server

1) Install with Composer

~~~php
"require": {
    "nizsheanez/yii2-json-rpc": "1.*",
},

php composer.phar update
~~~

2) Add action to controller

~~~php
public function actions()
{
    return array(
        'index' => array(
            'class' => '\nizsheanez\JsonRpc\Action',
        ),
    );
}

public function sum($a, $b) {
	return $a + $b;
}
~~~

3) TEST:

function sendRPC(){
		$.ajax({
			url: 'http://www.cis.morgan.lan',
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

4) Enjoy!



