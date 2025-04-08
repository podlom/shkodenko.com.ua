<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2025
 */

namespace ShkodenkoComUa\App;


use Dotenv\Dotenv;

class DbComment
{
    private $dbConnection = null;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $dbHost = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASS'];
        $dbCharset = $_ENV['DB_CHARSET'] ?? 'utf8';

        $this->dbConnection = new \PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=' . $dbCharset . ';',
            $dbUser,
            $dbPass
        );
    }

    public function addComment($data)
    {
        try {
            $sql = "INSERT INTO `comments` SET `name` = ?, `email` = ?, `author_IP` = ?, `comment_text` = ?, `comment_approved` = ?";
            $stmt = $this->dbConnection->prepare($sql);
            $res = $stmt->execute([
                $data['name'],
                $data['email'],
                $data['author_IP'],
                $data['comment_text'],
                $data['comment_approved'],
            ]);

            return $res;
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";

            return false;
        }
    }
}