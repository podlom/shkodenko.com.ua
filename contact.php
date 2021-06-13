<?php
/**
 * Created by PhpStorm.
 * User: Тарас
 * Date: 16.12.2016
 * Time: 22:17
 */

$mailRes = false;
if (!empty($_POST)) {
    $msg = date('r') . ' $_POST data: ' . var_export($_POST, 1) . PHP_EOL . '$_SERVER: ' . var_export($_SERVER, 1) . PHP_EOL;
    $mailRes = mail('podlom@gmail.com', $_SERVER['HTTP_HOST'] . ' contact form', $msg);
	$logFile = dirname(__FILE__) . '/log/contact.txt';
	error_log($msg, 3, $logFile);
}
header("Content-type: application/json");
echo json_encode(['res' => true]);
