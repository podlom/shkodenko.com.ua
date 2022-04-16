<?php
/**
 * Created by PhpStorm.
 * User: Тарас
 * Date: 16.12.2016
 * Time: 22:17
 */

$mailRes = false;
if (!empty($_POST)) {
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        // @see: https://developers.google.com/recaptcha/docs/verify
        $postdata = http_build_query(
            [
                'secret' => '6LeqNXwfAAAAAPaxwaPYT98VhbQr_1oEstNblqi_',
                'response' => $_POST['g-recaptcha-response'],
                // 'remoteip' => 'Optional. The user's IP address.',
            ]
        );
        $opts = [
            'http' =>
                [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata,
                ],
        ];
        $context = stream_context_create($opts);
        $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        //
        if ($result) {
            $msg = date('r') . ' $_POST data: ' . var_export($_POST, 1) . PHP_EOL . ' $_SERVER: ' . var_export($_SERVER, 1) . PHP_EOL;
            $mailRes = mail('podlom@gmail.com', $_SERVER['HTTP_HOST'] . ' contact form', $msg);
            $logFile = dirname(__FILE__) . '/log/contact.txt';
            if (file_exists($logFile) && is_writeable($logFile)) {
                error_log($msg, 3, $logFile);
            } else {
                error_log($msg);
            }
        } else {
            // TODO: SPAM
        }
    }
}
header("Content-type: application/json");
echo json_encode(['res' => true]);
