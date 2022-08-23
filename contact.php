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
                'secret' => '***REMOVED***',
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

            require_once 'src/DbComment.php';

            $dbComments = new ShkodenkoComUa\App\DbComment();
            $dbComments->addComment([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_text' => $_POST['message'],
                'comment_approved' => 0,
            ]);

        } else {
            // TODO: SPAM
        }
    }
}
header("Content-type: application/json");
echo json_encode(['res' => true]);
