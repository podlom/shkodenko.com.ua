<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2025
 *
 * Created by PhpStorm.
 * User: Тарас
 * Date: 16.12.2016
 * Time: 22:17
 */

require_once __DIR__ . '/vendor/autoload.php'; // Якщо використовуєш Composer

use ShkodenkoComUa\App\DbComment;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$googleReCaptchaSecretKey = $_ENV['GOOGLE_RECAPTCHA_SECRET'];

if (empty($googleReCaptchaSecretKey)) {
    header("Content-type: application/json");
    echo json_encode(['res' => false, 'msg' => 'Error: add GOOGLE_RECAPTCHA_SECRET to your .env config file']);
}

$mailRes = false;
if (!empty($_POST)) {
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        // @see: https://developers.google.com/recaptcha/docs/verify
        $postdata = http_build_query(
            [
                'secret' => $googleReCaptchaSecretKey,
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
            $msg = date('r') . ' $_POST data: ' . var_export($_POST, true) . PHP_EOL . ' $_SERVER: ' . var_export($_SERVER, true) . PHP_EOL;
            $mailRes = mail('podlom@gmail.com', $_SERVER['HTTP_HOST'] . ' contact form', $msg);
            $logFile = dirname(__FILE__) . '/log/contact.txt';
            if (file_exists($logFile) && is_writeable($logFile)) {
                error_log($msg, 3, $logFile);
            } else {
                error_log($msg);
            }

            $dbComments = new DbComment();
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
