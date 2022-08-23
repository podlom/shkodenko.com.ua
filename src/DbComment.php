<?php

namespace ShkodenkoComUa\App;


class DbComment
{
    private $dbConnection = null;

    public function __construct()
    {
        $this->dbConnection = new \PDO('mysql:host=localhost;dbname=shcomua6db;charset=utf8;',
            'shcomua6u',
            'olY2mYEBEqAvCue%'
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