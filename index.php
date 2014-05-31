<?php

# id Device el cúal nos proporciona xcode este lo demos colocar sin espacios para que funcione correctamente
$deviceToken = '4d0e85dd658a3c45442c6694b41366e1fd0a93f592cf1839e148cdd42cd93f70';

# password del certificado ck.pem
$passphrase = 'pushios';

# el mensaje que deseamos enviar a nuestros iphone o bien ipad
$message = 'My first push notification!';


$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

#  body de la push
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'default'
	);

$payload = json_encode($body);

$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

fclose($fp);
